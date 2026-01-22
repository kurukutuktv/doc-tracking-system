<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentStatus;
use App\Models\DocumentAttachment;
use App\Models\DocumentLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StoreIncomingDocumentRequest;
use App\Http\Requests\ForwardDocumentRequest;
use Illuminate\Http\Request;

class IncomingDocumentController extends Controller
{
    /**
     * Receive incoming document
     */
    public function store(StoreIncomingDocumentRequest $request)
    {
        $this->authorize('receive', Document::class);

        $data = $request->validated();

        /* ======================================
           1️⃣ CREATE DOCUMENT FIRST
        ====================================== */

        $document = Document::create([
            'direction' => 'INCOMING',
            'document_type_id' => $data['document_type_id'],
            'priority_level' => $data['priority_level'] ?? 'NORMAL',
            'received_date' => now(),

            'title' => $data['title'],
            'subject' => $data['subject'] ?? null,
            'description' => $data['description'] ?? null,
            'sender_name' => $data['sender_name'],

            'current_office_id' => Auth::user()->department_id,
            'status_id' => DocumentStatus::where('code', 'RECEIVED')->first()->id,
            'created_by' => Auth::id(),
        ]);

        /* ======================================
           2️⃣ UPLOAD & ATTACH FILES (HERE 👇)
        ====================================== */

        foreach ($request->file('files') as $file) {

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs(
                'documents/' . now()->year . '/' . now()->month,
                $filename,
                'public'
            );

            DocumentAttachment::create([
                'document_id'   => $document->id,
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'file_size'     => $file->getSize(),
            ]);
        }

        /* ======================================
           3️⃣ AUDIT LOG
        ====================================== */

        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'RECEIVED',
            'description' => 'Incoming document received with attachments',
        ]);

        return response()->json($document->load('attachments'), 201);
    }

    /**
     * Log & stamp document (generate control number)
     */
    public function log(Document $document)
    {
        $this->authorize('log', $document);

        // 1️⃣ Generate control number
        $controlNumber = 'CCC-' . now()->year . '-ADM-' . str_pad($document->id, 6, '0', STR_PAD_LEFT);

        // 2️⃣ Update registry fields
        $document->update([
            'control_number' => $controlNumber,
            'registry_date'  => now(),
            'status_id'      => DocumentStatus::where('code', 'LOGGED')->first()->id,
        ]);

        // 3️⃣ Rename uploaded file to match control number (if exists)
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {

            $oldPath = $document->file_path;
            $extension = pathinfo($oldPath, PATHINFO_EXTENSION);

            $newPath = dirname($oldPath) . '/' . $controlNumber . '.' . $extension;

            Storage::disk('public')->move($oldPath, $newPath);

            // Update file path in DB
            $document->update([
                'file_path' => $newPath,
            ]);
        }

        // 4️⃣ Audit log
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id'     => Auth::id(),
            'action'      => 'LOGGED',
            'description' => 'Document logged and stamped with control number',
        ]);

        return response()->json($document);
    }

    /**
     * Forward to concerned office
     */
    public function forward(ForwardDocumentRequest $request, Document $document)
    {
        $this->authorize('forward', $document);

        $data = $request->validated();

        $document->update([
            'current_office_id' => $data['office_id'],
            'status_id' => DocumentStatus::where('code', 'FORWARDED')->first()->id,
        ]);

        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'FORWARDED',
            'description' => 'Forwarded to concerned office',
        ]);

        return response()->json($document);
    }

    public function download(Document $document)
    {
        $this->authorize('view', $document);

        return response()->download(Storage::disk('public')->path($document->file_path));
    }

    public function replace(Request $request, DocumentAttachment $attachment)
    {
        $this->authorize('update', $attachment->document);

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        Storage::disk('public')->delete($attachment->file_path);

        $file = $request->file('file');
        $path = $file->store('documents/replacements', 'public');

        $attachment->update([
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
        ]);

        return back();
    }

    public function destroy(DocumentAttachment $attachment)
    {
        $this->authorize('update', $attachment->document);

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back();
    }
}
