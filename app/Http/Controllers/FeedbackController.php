<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    public function showForm(Request $request)
    {
        $user = Auth::user();
        $studentFaculty = $user ? $user->studentFaculty : null;

        $threads = Feedback::query()
            ->threads()
            ->with(['user', 'replies'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->input('q'));
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->input('category'));
            })
            ->latest()
            ->paginate(10)
            ->appends($request->except('page'));

        return view('feedback.form', [
            'user' => $user,
            'studentFaculty' => $studentFaculty,
            'threads' => $threads,
            'categoryOptions' => Feedback::categoryOptions(),
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:120',
            'category' => ['required', 'string', Rule::in(array_keys(Feedback::categoryOptions()))],
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $studentFaculty = $user ? $user->studentFaculty : null;

        $thread = Feedback::create([
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'parent_id' => null,
            'type' => 'thread',
            'category' => $request->input('category'),
            'course' => $studentFaculty->course ?? null,
            'role' => $studentFaculty->role ?? null,
            'is_anonymous' => false,
            'status' => 'open',
            'message' => $request->input('message'),
        ]);

        return redirect()->route('feedback.show', $thread->id)
            ->with('success', 'Topic posted successfully.');
    }

    public function show($id)
    {
        $thread = Feedback::query()
            ->threads()
            ->with(['user', 'replies.user'])
            ->findOrFail($id);

        return view('feedback.show', compact('thread'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $thread = Feedback::query()->threads()->findOrFail($id);

        if ($thread->status !== 'open') {
            return redirect()->route('feedback.show', $thread->id)
                ->with('error', 'Replies are disabled for this topic.');
        }

        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $studentFaculty = $user ? $user->studentFaculty : null;

        Feedback::create([
            'user_id' => $user->id,
            'title' => null,
            'parent_id' => $thread->id,
            'type' => 'reply',
            'category' => $thread->category,
            'course' => $studentFaculty->course ?? null,
            'role' => $studentFaculty->role ?? null,
            'is_anonymous' => false,
            'status' => 'open',
            'message' => $request->input('message'),
        ]);

        return redirect()->route('feedback.show', $thread->id)
            ->with('success', 'Reply posted.');
    }

    public function adminList(Request $request)
    {
        $query = Feedback::query()
            ->threads()
            ->with(['user', 'replies']);

        if ($request->filled('user')) {
            $userSearch = $request->input('user');
            $query->whereHas('user', function ($q) use ($userSearch) {
                $q->where('name', 'like', "%$userSearch%")
                    ->orWhere('email', 'like', "%$userSearch%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
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
        $categoryOptions = Feedback::categoryOptions();
        return view('feedback.admin', compact('feedbacks', 'categoryOptions'));
    }

    public function adminShow($id)
    {
        $feedback = Feedback::query()
            ->threads()
            ->with(['user', 'replies.user'])
            ->findOrFail($id);

        $categoryOptions = Feedback::categoryOptions();

        return view('feedback.admin_show', compact('feedback', 'categoryOptions'));
    }

    public function adminReply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $thread = Feedback::query()->threads()->findOrFail($id);
        $admin = Auth::user();

        if (!$admin) {
            abort(403);
        }

        Feedback::create([
            'user_id' => $admin->id,
            'title' => null,
            'parent_id' => $thread->id,
            'type' => 'reply',
            'category' => $thread->category,
            'course' => $thread->course,
            'role' => $admin->role,
            'is_anonymous' => false,
            'status' => 'open',
            'message' => $request->input('message'),
        ]);

        return redirect()->back()
            ->with('success', 'Admin message sent.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,resolved,closed',
        ]);

        $feedback = Feedback::query()->threads()->findOrFail($id);
        $feedback->update([
            'status' => $request->input('status'),
        ]);

        return redirect()->back()->with('success', 'Topic status updated.');
    }

    public function followUp($id)
    {
        $feedback = Feedback::with('user')->findOrFail($id);
        // You can implement follow-up logic here (e.g., send email, mark as resolved)
        return view('feedback.followup', compact('feedback'));
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        // Delete all replies when deleting a topic.
        if ($feedback->type === 'thread') {
            Feedback::where('parent_id', $feedback->id)->delete();
        }
        $feedback->delete();
        return redirect()->route('feedback.admin')->with('success', 'Feedback deleted successfully.');
    }
}
