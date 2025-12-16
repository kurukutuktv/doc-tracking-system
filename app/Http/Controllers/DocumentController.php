<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ApprovalHierarchy;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* =========================
     * HELPERS
     * ========================= */
    private function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    private function logMovement(Document $document, string $action, array $meta = [])
    {
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'meta' => $meta ?: null,
        ]);
    }

    private function userCanSee(Document $document, User $user): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($document->created_by === $user->id) return true;

        if ($document->is_memo) {
            if ($document->audience_type === 'all') return true;
            if ($document->audience_type === 'users') {
                return in_array($user->id, $document->audience_users ?? []);
            }
        }

        if ($document->is_for_approval) {
            return $document->current_approver_id === $user->id;
        }

        return false;
    }

    /* =========================
     * INDEX
     * ========================= */

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Document::with(['department', 'creator', 'currentApprover'])->latest();

        // Department filter
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (!$this->isAdmin($user)) {
            $documents = $query->get()
                ->filter(fn($doc) => $this->userCanSee($doc, $user))
                ->values();
        } else {
            $documents = $query->paginate(10);
        }

        $departments = Department::orderBy('name')->get();

        return view('documents.index', compact('documents', 'departments'));
    }



    /* =========================
     * CREATE
     * ========================= */

    public function create()
    {
        $this->authorize('create', Document::class);

        $users = User::orderBy('last_name')->get();
        $departments = Department::orderBy('name')->get();
        $userDepartmentId = Auth::user()->department_id;

        return view('documents.create', compact(
            'users',
            'departments',
            'userDepartmentId'
        ));
    }

    /* =========================
     * STORE
     * ========================= */

    public function store(Request $request)
    {
        $this->authorize('create', Document::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480',
            'doc_type' => 'required|in:memo,approval',
            'audience_type' => 'nullable|in:all,users',
            'audience_users' => 'nullable|array',
            'target_department_id' => 'required|exists:departments,id',
        ]);

        $filePath = $request->file('file')
            ? $request->file('file')->store('documents')
            : null;

        $document = Document::create([
            'tracking_number' => "DOC-" . strtoupper(Str::random(6)) . "-" . time(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'created_by' => Auth::id(),
            'department_id' => $request->target_department_id, // ✅ REQUIRED
            'is_memo' => $request->doc_type === 'memo',
            'is_for_approval' => $request->doc_type === 'approval',
            'status' => $request->doc_type === 'memo'
                ? 'information'
                : 'pending',
        ]);

        /* ---------- MEMO ---------- */
        if ($document->is_memo) {
            $document->audience_type = $validated['audience_type'] ?? 'all';
            $document->audience_users = $validated['audience_users'] ?? [];
            $document->save();

            $this->logMovement($document, 'Memo published');
        }

        /* ---------- APPROVAL ---------- */
        if ($document->is_for_approval) {

            $hierarchy = ApprovalHierarchy::where('department_id', $document->department_id)
                ->orderBy('level')
                ->get();

            if ($hierarchy->isEmpty()) {
                return back()->with('error', 'No approval hierarchy configured.');
            }

            $firstLevel = $hierarchy->first();

            $firstApprover = User::whereHas('roles', function ($q) use ($firstLevel) {
                $q->where('id', $firstLevel->role_id);
            })->first();

            if (!$firstApprover) {
                return back()->with('error', 'No approver assigned for first level.');
            }

            $document->current_approver_id = $firstApprover->id;
            $document->current_approval_level = 1;
            $document->save();

            $this->logMovement($document, 'Submitted for approval', [
                'level' => 1,
                'role' => $firstLevel->role->name,
            ]);
        }

        return redirect()->route('documents.index')
            ->with('success', 'Document created successfully.');
    }

    /* =========================
     * SHOW
     * ========================= */

    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['creator', 'logs.user', 'currentApprover']);

        return view('documents.show', compact('document'));
    }

    /* =========================
     * APPROVE
     * ========================= */

    public function approve(Document $document)
    {
        $user = Auth::user();

        if (!$document->is_for_approval) {
            return back()->with('error', 'Not an approval document.');
        }

        if ($document->current_approver_id !== $user->id) {
            return back()->with('error', 'You are not the current approver.');
        }

        $hierarchy = ApprovalHierarchy::where('department_id', $document->department_id)
            ->orderBy('level')
            ->get();

        $nextLevel = $hierarchy->firstWhere(
            'level',
            $document->current_approval_level + 1
        );

        if ($nextLevel) {
            $nextApprover = User::whereHas('roles', function ($q) use ($nextLevel) {
                $q->where('id', $nextLevel->role_id);
            })->first();

            if (!$nextApprover) {
                return back()->with('error', 'Next approver not found.');
            }

            $document->current_approval_level++;
            $document->current_approver_id = $nextApprover->id;
            $document->status = 'in_review';
            $document->save();

            $this->logMovement($document, "Approved by {$user->name}");

            return back()->with('success', 'Approved and forwarded.');
        }

        /* ---------- FINAL APPROVAL ---------- */
        $document->status = 'completed';
        $document->current_approver_id = null;
        $document->save();

        $this->logMovement($document, "Final approval by {$user->name}");

        return back()->with('success', 'Document fully approved.');
    }

    /* =========================
     * REJECT
     * ========================= */

    public function reject(Document $document)
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }


        $document->status = 'rejected';
        $document->current_approver_id = null;
        $document->save();

        $this->logMovement($document, "Rejected by {$user->name}");

        return back()->with('success', 'Document rejected.');
    }
    /* =========================
     * ACKNOWLEDGE
     * ========================= */

    public function acknowledge(Document $document)
    {
        $user = Auth::user();

        if (!$document->is_memo) {
            return back()->with('error', 'Not a memo.');
        }

        if (!$this->userCanSee($document, $user)) {
            abort(403);
        }

        if ($document->isAcknowledgedBy($user->id)) {
            return back();
        }

        $document->acknowledge($user->id);

        $this->logMovement($document, 'Acknowledged');

        return back()->with('success', 'Memo acknowledged.');
    }
}
