<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentStatus;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'incoming_today' => Document::whereDate('received_date', today())->count(),
            'unlogged' => Document::whereHas('status', fn($q) => $q->where('code', 'RECEIVED'))->count(),
            'forwarded' => Document::whereHas('status', fn($q) => $q->where('code', 'FORWARDED'))->count(),
            'outgoing_pending' => Document::whereHas('status', fn($q) => $q->where('code', 'LOGGED'))
                ->where('direction', 'OUTGOING')->count(),
            'archived' => Document::whereHas('status', fn($q) => $q->where('code', 'ARCHIVED'))->count(),
        ];

        $actionQueue = Document::whereHas('status', function ($q) {
            $q->whereIn('code', ['RECEIVED', 'LOGGED']);
        })->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'actionQueue'));
    }
}
