<?php

namespace App\Http\Controllers\Office;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentStatus;
use App\Models\DocumentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PrepareReplyRequest;


class DocumentInboxController extends Controller
{
    /**
     * View documents forwarded to office
     */
    public function index()
    {
        return Document::where('current_office_id', Auth::user()->department_id)
            ->whereHas('status', fn($q) => $q->where('code', 'FORWARDED'))
            ->get();
    }

    /**
     * Acknowledge receipt
     */
    public function acknowledge(Document $document)
    {
        $this->authorize('acknowledge', $document);

        $document->update([
            'status_id' => DocumentStatus::where('code', 'ACKNOWLEDGED')->first()->id,
        ]);

        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'ACKNOWLEDGED',
            'description' => 'Document acknowledged',
        ]);

        return response()->json($document);
    }

    /**
     * Prepare reply
     */
    public function prepareReply(PrepareReplyRequest $request, Document $parent)
    {
        $this->authorize('prepareReply', $parent);

        $data = $request->validated();

        $reply = Document::create([
            'direction' => 'OUTGOING',
            'parent_document_id' => $parent->id,
            'document_type_id' => $data['document_type_id'],
            'priority_level' => $data['priority_level'] ?? 'NORMAL',

            'title' => $data['title'],
            'subject' => $data['subject'] ?? null,
            'description' => $data['description'] ?? null,
            'file_path' => $data['file_path'],

            'current_office_id' => Auth::user()->department_id,
            'status_id' => DocumentStatus::where('code', 'REPLY_PREPARED')->first()->id,
            'created_by' => Auth::id(),
        ]);

        DocumentLog::create([
            'document_id' => $reply->id,
            'user_id' => Auth::id(),
            'action' => 'REPLY_PREPARED',
            'description' => 'Reply prepared by office',
        ]);

        return response()->json($reply, 201);
    }
}
