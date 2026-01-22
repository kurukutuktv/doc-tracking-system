<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentStatus;
use App\Models\DocumentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutgoingDocumentController extends Controller
{
    /**
     * Log outgoing reply
     */
    public function log(Document $document)
    {
        $this->authorize('log', $document);

        $document->update([
            'control_number' => 'CCC-' . now()->year . '-ADM-' . str_pad($document->id, 6, '0', STR_PAD_LEFT),
            'registry_date' => now(),
            'status_id' => DocumentStatus::where('code', 'LOGGED')->first()->id,
        ]);

        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'LOGGED',
            'description' => 'Outgoing document logged',
        ]);

        return response()->json($document);
    }

    /**
     * Transmit outgoing document
     */
    public function transmit(Request $request, Document $document)
    {
        $this->authorize('transmit', $document);

        $document->update([
            'transmission_mode_id' => $request->transmission_mode_id,
            'sent_date' => now(),
            'status_id' => DocumentStatus::where('code', 'TRANSMITTED')->first()->id,
        ]);

        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'TRANSMITTED',
            'description' => 'Document transmitted',
        ]);

        return response()->json($document);
    }

    /**
     * Archive document
     */
    public function archive(Document $document)
    {
        $this->authorize('archive', $document);

        $document->update([
            'status_id' => DocumentStatus::where('code', 'ARCHIVED')->first()->id,
        ]);

        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'ARCHIVED',
            'description' => 'Document archived',
        ]);

        return response()->json($document);
    }
}
