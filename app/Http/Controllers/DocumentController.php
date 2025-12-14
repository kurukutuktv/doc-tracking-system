<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Safe role checker
     */
    private function hasRole($user, $role)
    {
        return $user && method_exists($user, 'hasRole') && $user->hasRole($role);
    }

    /* ---------- Helpers ---------- */

    private function logMovement(Document $document, string $action, array $meta = [])
    {
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'meta' => $meta ?: null,
        ]);
    }

    private function userCanSee(Document $document, $user): bool
    {
        // admin sees all
        if (method_exists($user, 'hasRole') && $user->hasRole($user, 'admin')) {
            return true;
        }

        // creator can always see
        if ($document->created_by == $user->id) {
            return true;
        }

        // memo visibility
        if ($document->is_memo) {
            if ($document->audience_type === 'all') return true;
            if ($document->audience_type === 'users' && is_array($document->audience_users)) {
                return in_array($user->id, $document->audience_users);
            }
            // department logic omitted: implement if departments exist
            return false;
        }

        // approval visibility: current approver or future approver can see, or creator
        if ($document->is_for_approval) {
            if ($document->current_approver_id && $document->current_approver_id == $user->id) return true;
            if (is_array($document->next_approver_ids) && in_array($user->id, $document->next_approver_ids)) return true;
            return false;
        }

        // fallback
        return false;
    }

    /* ---------- CRUD & Listing ---------- */

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) abort(401);

        $query = Document::query()->latest();

        // ADMIN — sees all docs
        if ($this->hasRole($user, 'admin')) {
            $documents = $query->paginate(12);
        } else {
            // non-admin must be filtered manually
            $all = $query->get();

            // filter using your visibility rules
            $filtered = $all->filter(fn($doc) => $this->userCanSee($doc, $user));

            // optional: paginate manually if needed, but returning the collection is okay for now
            $documents = $filtered;
        }

        return view('documents.index', [
            'documents' => $documents,
        ]);
    }


    public function create()
    {
        $this->authorize('create', Document::class);

        // users for audience & approver selection
        $users = User::orderBy('name')->get();

        // pass to blade
        return view('documents.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Document::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480',
            'doc_type' => 'required|in:memo,approval',
            'audience_type' => 'nullable|in:all,users,department',
            'audience_users' => 'nullable|array',
            'approval_chain' => 'nullable|array',
            'approval_chain.*' => 'nullable|exists:users,id',
        ]);

        $filePath = $request->file('file') ? $request->file('file')->store('documents') : null;

        $doc = Document::create([
            'tracking_number' => "DOC-" . strtoupper(Str::random(6)) . "-" . time(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'created_by' => Auth::id(),
            'is_memo' => $request->doc_type === 'memo',
            'is_for_approval' => $request->doc_type === 'approval',
            'status' => $request->doc_type === 'memo' ? 'information' : 'pending',
        ]);

        // Handle memo audience
        if ($request->doc_type === 'memo') {
            $doc->audience_type = $request->audience_type ?? 'all';
            if ($request->audience_type === 'users') {
                $doc->audience_users = $request->audience_users ?: [];
            }
            $doc->save();
            $this->logMovement($doc, 'Memo created and published');
        }

        // Handle approval chain
        if ($request->doc_type === 'approval') {
            $chain = $request->approval_chain ?: [];
            $chain = array_values(array_filter($chain)); // remove empty
            if (!empty($chain)) {
                $doc->current_approver_id = $chain[0];
                $doc->next_approver_ids = array_slice($chain, 1);
                $doc->status = 'pending';
                $doc->save();

                $this->logMovement($doc, 'Document submitted for approval', ['chain' => $chain]);
            } else {
                // no chain provided — treat as pending but notify admin
                $this->logMovement($doc, 'Document submitted for approval (no chain)');
            }
        }

        return redirect()->route('documents.index')->with('success', 'Document saved.');
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);

        // load logs + creator + current approver
        $document->load('logs.user', 'creator', 'currentApprover');

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $this->authorize('update', $document);
        $users = User::orderBy('name')->get();
        return view('documents.edit', compact('document', 'users'));
    }

    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480',
            'doc_type' => 'required|in:memo,approval',
            'audience_type' => 'nullable|in:all,users,department',
            'audience_users' => 'nullable|array',
            'approval_chain' => 'nullable|array',
            'approval_chain.*' => 'nullable|exists:users,id',
        ]);

        if ($request->hasFile('file')) {
            $document->file_path = $request->file('file')->store('documents');
        }

        $document->title = $request->title;
        $document->description = $request->description;
        $document->is_memo = $request->doc_type === 'memo';
        $document->is_for_approval = $request->doc_type === 'approval';

        if ($request->doc_type === 'memo') {
            $document->audience_type = $request->audience_type ?? 'all';
            $document->audience_users = $request->audience_users ?: null;
            $document->status = 'information';
        } else {
            $chain = $request->approval_chain ?: [];
            $chain = array_values(array_filter($chain));
            $document->current_approver_id = $chain[0] ?? null;
            $document->next_approver_ids = array_slice($chain, 1);
            $document->status = 'pending';
        }

        $document->save();
        $this->logMovement($document, 'Document updated');

        return redirect()->route('documents.show', $document->id)->with('success', 'Document updated.');
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);
        $document->delete();
        $this->logMovement($document, 'Document deleted');
        return redirect()->route('documents.index')->with('success', 'Document removed.');
    }

    /* ---------- Acknowledge & Approval Flow ---------- */

    public function acknowledge(Document $document)
    {
        $user = Auth::user();
        if (!$user) abort(401);

        // only memos audience can acknowledge
        if (!$document->is_memo) {
            return back()->with('error', 'This document is not an announcement.');
        }

        $ack = $document->acknowledged_by ?: [];
        if (!in_array($user->id, $ack)) {
            $ack[] = $user->id;
            $document->acknowledged_by = $ack;
            $document->save();
            $this->logMovement($document, "Acknowledged by {$user->name}");
        }

        return back()->with('success', 'Acknowledged.');
    }

    public function approve(Request $request, Document $document)
    {
        $user = Auth::user();
        if (!$user) abort(401);

        if (!$document->is_for_approval) {
            return back()->with('error', 'Document is not routed for approvals.');
        }

        // check if user is permitted to approve (must be current approver)
        if ($document->current_approver_id != $user->id) {
            return back()->with('error', 'You are not the current approver for this document.');
        }

        // perform approval: move to next approver or complete
        $next = $document->next_approver_ids ?: [];

        if (!empty($next)) {
            $document->approved_level_1_by = $document->approved_level_1_by ?? ($user->id);
            // move head
            $document->current_approver_id = array_shift($next);
            $document->next_approver_ids = $next;
            $document->status = 'in_review';
            $document->save();

            $this->logMovement($document, "Approved by {$user->name}", ['next' => $document->current_approver_id]);
            return back()->with('success', 'Approved and forwarded to next approver.');
        } else {
            // no next approvers — finalize
            // set final approved field in sequence if you want (approved_level_3_by etc)
            $document->approved_level_3_by = $user->id;
            $document->current_approver_id = null;
            $document->next_approver_ids = null;
            $document->status = 'completed';
            $document->save();

            $this->logMovement($document, "Final approval by {$user->name}");
            return back()->with('success', 'Document fully approved (completed).');
        }
    }

    public function reject(Request $request, Document $document)
    {
        $user = Auth::user();
        if (!$user) abort(401);

        // only current approver can reject (or admins via policy)
        if ($document->current_approver_id != $user->id && !(method_exists($user, 'hasRole') && $this->hasRole($user, 'admin'))) {
            return back()->with('error', 'You are not authorized to reject this document.');
        }

        $document->status = 'rejected';
        $document->current_approver_id = null;
        $document->next_approver_ids = null;
        $document->save();

        $this->logMovement($document, "Rejected by {$user->name}");

        return back()->with('success', 'Document rejected.');
    }
}
