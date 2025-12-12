<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentLog;
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

    /**
     * Get current user's single role safely
     */
    private function getUserRole($user)
    {
        return $user && method_exists($user, 'roles')
            ? optional($user->roles->first())->name
            : null;
    }

    /**
     * List documents for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) abort(401, 'User not authenticated.');

        if ($this->hasRole($user, 'admin')) {
            $documents = Document::with('creator')->latest()->get();
        }
        elseif ($this->hasRole($user, 'approver1')) {
            $documents = Document::where('approver_level', 1)
                ->where('status', 'pending')
                ->get();
        }
        elseif ($this->hasRole($user, 'approver2')) {
            $documents = Document::where('approver_level', 2)
                ->where('status', 'in_review')
                ->get();
        }
        elseif ($this->hasRole($user, 'approver3')) {
            $documents = Document::where('approver_level', 3)
                ->where('status', 'in_review')
                ->get();
        }
        elseif ($this->hasRole($user, 'creator')) {
            $documents = Document::where('created_by', $user->id)->get();
        }
        else {
            $documents = Document::where('status', 'completed')->get();
        }

        return view('documents.index', compact('documents'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Document::class);
        return view('documents.create');
    }

    /**
     * Store the document
     */
    public function store(Request $request)
    {
        $this->authorize('create', Document::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480'
        ]);

        $filePath = $request->file('file')
            ? $request->file('file')->store('documents')
            : null;

        $tracking = "DOC-" . strtoupper(Str::random(6)) . "-" . time();

        $document = Document::create([
            'tracking_number' => $tracking,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'status' => 'pending',
            'approver_level' => 1,
            'created_by' => Auth::id(),
        ]);

        $this->logMovement($document, "Document created by " . Auth::user()->name);

        return redirect()->route('documents.index')->with('success', 'Document created.');
    }

    /**
     * Show
     */
    public function show(Document $document)
    {
        $this->authorize('view', $document);
        return view('documents.show', compact('document'));
    }

    /**
     * Edit
     */
    public function edit(Document $document)
    {
        $this->authorize('update', $document);
        return view('documents.edit', compact('document'));
    }

    /**
     * Update
     */
    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480'
        ]);

        if ($request->hasFile('file')) {
            $document->file_path = $request->file('file')->store('documents');
        }

        $document->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $this->logMovement($document, "Document updated by " . Auth::user()->name);

        return redirect()->route('documents.index')->with('success', 'Document updated.');
    }

    /**
     * Delete
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }

    /**
     * Multi-level Approval
     */
    public function approve(Document $document)
    {
        $user = Auth::user();
        $role = $this->getUserRole($user);

        if (!$role) {
            return back()->with('error', 'User has no assigned role.');
        }

        // Approver 1 → level 2
        if ($role === 'approver1' && $document->approver_level == 1) {
            $document->update([
                'approved_level_1_by' => $user->id,
                'approver_level' => 2,
                'status' => 'in_review'
            ]);
        }
        // Approver 2 → level 3
        elseif ($role === 'approver2' && $document->approver_level == 2) {
            $document->update([
                'approved_level_2_by' => $user->id,
                'approver_level' => 3,
            ]);
        }
        // Approver 3 → FINAL
        elseif ($role === 'approver3' && $document->approver_level == 3) {
            $document->update([
                'approved_level_3_by' => $user->id,
                'status' => 'completed',
            ]);
        }
        else {
            return back()->with('error', 'You cannot approve this document.');
        }

        $this->logMovement($document, "Approved by {$user->name}");

        return back()->with('success', 'Document approved.');
    }

    /**
     * Reject
     */
    public function reject(Document $document)
    {
        $this->authorize('reject', $document);

        $document->update([
            'status' => 'rejected'
        ]);

        $this->logMovement($document, "Rejected by " . Auth::user()->name);

        return back()->with('success', 'Document rejected.');
    }

    /**
     * Log Activity
     */
    private function logMovement(Document $document, $action)
    {
        // Uncomment when DocumentLog model + table ready
        /*
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => $action,
        ]);
        */
    }
}