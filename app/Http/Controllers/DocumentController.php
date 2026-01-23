<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Department;
use App\Models\User;
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

    /* =========================
     * HELPERS
     * ========================= */

    private function logMovement(Document $document, string $action, array $meta = [])
    {
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'meta' => $meta ?: null,
        ]);
    }

    /* =========================
     * INDEX
     * ========================= */

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Document::with(['department', 'creator'])
            ->latest();

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔐 POLICY-BASED VISIBILITY
        if ($user->role === 'admin') {
            $documents = $query->paginate(10);
        } else {
            $documents = $query
                ->where('created_by', $user->id)
                ->paginate(10);
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

        $departments = Department::orderBy('name')->get();
        $userDepartmentId = Auth::user()->department_id;

        return view('documents.create', compact(
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
            'doc_type' => 'required|in:memo,document',
            'audience_type' => 'nullable|in:all,users',
            'audience_users' => 'nullable|array',
            'target_department_id' => 'required|exists:departments,id',
        ]);

        $filePath = $request->file('file')
            ? $request->file('file')->store('documents')
            : null;

        $document = Document::create([
            'tracking_number' => 'DOC-' . strtoupper(Str::random(6)) . '-' . time(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $filePath,
            'created_by' => Auth::id(),
            'department_id' => $validated['target_department_id'],
            'is_memo' => $validated['doc_type'] === 'memo',
            'status' => 'submitted',
        ]);

        if ($document->is_memo) {
            $document->update([
                'audience_type' => $validated['audience_type'] ?? 'all',
                'audience_users' => $validated['audience_users'] ?? [],
                'status' => 'information',
            ]);

            $this->logMovement($document, 'Memo published');
        } else {
            $this->logMovement($document, 'Document submitted');
        }

        return redirect()
            ->route('documents.index')
            ->with('success', 'Document created successfully.');
    }

    /* =========================
     * SHOW
     * ========================= */

    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['creator', 'logs.user']);

        return view('documents.show', compact('document'));
    }

    /* =========================
     * ACKNOWLEDGE (MEMO)
     * ========================= */

    public function acknowledge(Document $document)
    {
        $this->authorize('acknowledge', $document);

        if ($document->isAcknowledgedBy(Auth::id())) {
            return back();
        }

        $document->acknowledge(Auth::id());

        $this->logMovement($document, 'Acknowledged');

        return back()->with('success', 'Memo acknowledged.');
    }

    /* =========================
     * REJECT
     * ========================= */

    public function reject(Document $document)
    {
        $this->authorize('reject', $document);

        $document->update([
            'status' => 'rejected',
        ]);

        $this->logMovement($document, 'Rejected');

        return back()->with('success', 'Document rejected.');
    }

    /* =========================
     * DELETE
     * ========================= */

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        $document->delete();

        $this->logMovement($document, 'Deleted');

        return redirect()
            ->route('documents.index')
            ->with('success', 'Document deleted.');
    }
}
