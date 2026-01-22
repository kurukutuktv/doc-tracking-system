<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentAttachment;

class DocumentFileController extends Controller
{
    /**
     * Download document file
     */
    public function download(Document $document)
    {
        $this->authorize('view', $document);

        return response()->download(
            Storage::disk('public')->path($document->file_path)
        );
    }

    /**
     * View document file (PDF / image in browser)
     */
    public function view(Document $document)
    {
        $this->authorize('view', $document);

        return response()->file(
            Storage::disk('public')->path($document->file_path)
        );
    }

    public function preview(DocumentAttachment $attachment)
    {
        $this->authorize('view', $attachment->document);

        return response()->file(
            Storage::disk('public')->path($attachment->file_path)
        );
    }
}
