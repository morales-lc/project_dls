<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $sf = $user->studentFaculty ?? null;
        $histories = collect();
        if ($sf) {
            $histories = \App\Models\SearchHistory::where('student_faculty_id', $sf->id)
                ->orderByDesc('created_at')
                ->paginate(15);
        }

        return view('history', compact('histories'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        $sf = $user->studentFaculty ?? null;
        $history = \App\Models\SearchHistory::findOrFail($id);
        if (!$sf || $history->student_faculty_id !== $sf->id) {
            abort(403);
        }
        $history->delete();
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('history')->with('success', 'History item deleted');
    }

    public function clearAll()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        $sf = $user->studentFaculty ?? null;
        if ($sf) {
            \App\Models\SearchHistory::where('student_faculty_id', $sf->id)->delete();
        }
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('history')->with('success', 'All history cleared');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);
        $sf = $user->studentFaculty ?? null;
        $history = \App\Models\SearchHistory::findOrFail($id);
        if (!$sf || $history->student_faculty_id !== $sf->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'query' => 'nullable|string|max:500'
        ]);
        $history->query = $data['query'] ?? '';
        $history->save();
        return response()->json(['success' => true, 'query' => $history->query]);
    }
}
