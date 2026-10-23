<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Contract;
use App\Models\ContractDocument;

class DashboardController extends Controller
{
    public function index()
    {
        $pending = Contract::where('status', Contract::STATUS_PENDING)->count();
        $approvedMonth = Contract::where('status', Contract::STATUS_APPROVED)
            ->whereYear('approved_at', now()->year)
            ->whereMonth('approved_at', now()->month)
            ->count();
        $rejectedMonth = Contract::where('status', Contract::STATUS_REJECTED)
            ->whereYear('approved_at', now()->year)
            ->whereMonth('approved_at', now()->month)
            ->count();
        $documentsToday = ContractDocument::whereDate('created_at', now()->toDateString())->count();
        $latestDocument = ContractDocument::query()->latest()->first();

        $needsAction = Contract::with('creator')
            ->where('status', Contract::STATUS_PENDING)
            ->latest()
            ->take(3)
            ->get();

        $statusCounts = [
            'approved' => Contract::where('status', Contract::STATUS_APPROVED)->count(),
            'pending' => $pending,
            'rejected' => Contract::where('status', Contract::STATUS_REJECTED)->count(),
        ];

        $activities = Activity::with('user')->latest()->take(3)->get();

        return view('dashboard.index', [
            'monthLabel' => now()->translatedFormat('F Y'),
            'pending' => $pending,
            'approvedMonth' => $approvedMonth,
            'rejectedMonth' => $rejectedMonth,
            'documentsToday' => $documentsToday,
            'latestDocument' => $latestDocument,
            'needsAction' => $needsAction,
            'statusCounts' => $statusCounts,
            'statusMax' => max($statusCounts) ?: 1,
            'activities' => $activities,
        ]);
    }

    public function pulse()
    {
        return response()->json([
            'latest' => Contract::query()->max('updated_at'),
            'count' => Contract::query()->count(),
        ]);
    }
}
