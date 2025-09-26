<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;
use App\Models\StudentFaculty;

class FeedbackController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();
        $studentFaculty = $user ? $user->studentFaculty : null;
        return view('feedback.form', [
            'user' => $user,
            'studentFaculty' => $studentFaculty
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'is_anonymous' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $studentFaculty = $user ? $user->studentFaculty : null;

        $feedback = Feedback::create([
            'user_id' => $request->input('is_anonymous') ? null : ($user ? $user->id : null),
            'course' => $studentFaculty->course ?? null,
            'role' => $studentFaculty->role ?? null,
            'is_anonymous' => $request->input('is_anonymous') ? true : false,
            'message' => $request->input('message'),
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }

    public function adminList(Request $request)
    {
        $query = Feedback::with('user');

        if ($request->filled('user')) {
            $userSearch = $request->input('user');
            $query->whereHas('user', function($q) use ($userSearch) {
                $q->where('name', 'like', "%$userSearch%")
                  ->orWhere('email', 'like', "%$userSearch%");
            });
        }
        if ($request->filled('course')) {
            $query->where('course', 'like', "%" . $request->input('course') . "%");
        }
        if ($request->filled('role')) {
            $query->where('role', 'like', "%" . $request->input('role') . "%");
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $feedbacks = $query->latest()->paginate(20)->appends($request->except('page'));
        return view('feedback.admin', compact('feedbacks'));
    }

    public function followUp($id)
    {
        $feedback = Feedback::with('user')->findOrFail($id);
        // You can implement follow-up logic here (e.g., send email, mark as resolved)
        return view('feedback.followup', compact('feedback'));
    }

    public function delete($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();
        return redirect()->route('feedback.admin')->with('success', 'Feedback deleted successfully.');
    }
}
