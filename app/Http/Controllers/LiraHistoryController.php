<?php

namespace App\Http\Controllers;

use App\Models\LiraRequest;
use Illuminate\Http\Request;

class LiraHistoryController extends Controller
{
    public function index(Request $request)
    {
        $tab = $this->resolveTab((string) $request->query('tab', 'all'));

        $baseQuery = LiraRequest::query()
            ->with(['catalog:id,title,author,call_number'])
            ->where('user_id', $request->user()->id)
            ->borrowHistory();

        $itemsQuery = clone $baseQuery;

        if ($tab === 'currently-borrowing') {
            $itemsQuery->where('loan_status', 'borrowed');
        } elseif ($tab === 'returned') {
            $itemsQuery->where('loan_status', 'returned');
        } elseif ($tab === 'scanning-completed') {
            $itemsQuery
                ->where('action', 'scanning')
                ->successfulFulfillment()
                ->where(function ($query) {
                    $query->whereNull('loan_status')
                        ->orWhereNotIn('loan_status', ['borrowed', 'returned']);
                });
        } elseif ($tab === 'not-approved') {
            $itemsQuery->rejected();
        }

        $items = $itemsQuery
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'all' => (clone $baseQuery)->count(),
            'currently_borrowing' => (clone $baseQuery)->where('loan_status', 'borrowed')->count(),
            'returned' => (clone $baseQuery)->where('loan_status', 'returned')->count(),
            'scanning_completed' => (clone $baseQuery)
                ->where('action', 'scanning')
                ->successfulFulfillment()
                ->where(function ($query) {
                    $query->whereNull('loan_status')
                        ->orWhereNotIn('loan_status', ['borrowed', 'returned']);
                })
                ->count(),
            'not_approved' => (clone $baseQuery)->rejected()->count(),
        ];

        return view('lira-history.index', compact('items', 'summary', 'tab'));
    }

    private function resolveTab(string $tab): string
    {
        return match ($tab) {
            'successful' => 'scanning-completed',
            'completed' => 'scanning-completed',
            'failed' => 'not-approved',
            default => in_array($tab, ['all', 'currently-borrowing', 'returned', 'scanning-completed', 'not-approved'], true)
                ? $tab
                : 'all',
        };
    }
}