<?php

namespace App\Http\Controllers\Office;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;


class OfficeDashboardController extends Controller
{
    public function index()
    {
        $departmentId = Auth::user()->department_id;

        $stats = [
            'new' => Document::where('current_office_id', $departmentId)
                ->whereHas('status', fn($q) => $q->where('code', 'FORWARDED'))->count(),

            'for_reply' => Document::where('current_office_id', $departmentId)
                ->whereHas('status', fn($q) => $q->whereIn('code', ['ACKNOWLEDGED', 'FOR_REPLY']))->count(),

            'replied' => Document::where('created_by', Auth::id())
                ->where('direction', 'OUTGOING')->count(),
        ];

        $inbox = Document::where('current_office_id', $departmentId)
            ->latest()->take(10)->get();

        return view('office.dashboard', compact('stats', 'inbox'));
    }
}
