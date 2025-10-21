<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\LibrarySetting;
use App\Models\LibraryAnnouncement;

class LibraryContentController extends Controller
{
    public function manage()
    {
        $settings = LibrarySetting::singleton();
        $announcements = LibraryAnnouncement::orderBy('position')->get();
        return view('library-content-management', compact('settings', 'announcements'));
    }

    public function updateGif(Request $request)
    {
        $request->validate([
            'gif' => 'nullable|file|mimetypes:image/gif|max:4096',
        ], [
            'gif.mimetypes' => 'Only GIF files are allowed.',
        ]);

        $settings = LibrarySetting::singleton();

        if ($request->hasFile('gif')) {
            // Remove previous gif if exists
            if ($settings->library_hours_gif) {
                Storage::disk('public')->delete($settings->library_hours_gif);
            }
            $path = $request->file('gif')->store('library', 'public');
            $settings->library_hours_gif = $path;
        } else if ($request->boolean('remove_gif')) {
            if ($settings->library_hours_gif) {
                Storage::disk('public')->delete($settings->library_hours_gif);
            }
            $settings->library_hours_gif = null;
        }

        $settings->save();

        return back()->with('success', 'Library hours GIF updated.');
    }

    public function storeAnnouncement(Request $request)
    {
        $data = $request->validate([
            'text' => 'required|string|max:500',
            'active' => 'sometimes|boolean',
        ]);

        $maxPos = LibraryAnnouncement::max('position') ?? 0;
        $data['position'] = $maxPos + 1;
        $data['active'] = $request->boolean('active', true);
        LibraryAnnouncement::create($data);

        return back()->with('success', 'Announcement added.');
    }

    public function updateAnnouncement(Request $request, int $id)
    {
        $announcement = LibraryAnnouncement::findOrFail($id);
        $data = $request->validate([
            'text' => 'required|string|max:500',
            'active' => 'sometimes|boolean',
        ]);
        $announcement->update([
            'text' => $data['text'],
            // If checkbox is unchecked, no 'active' is sent; default should be false
            'active' => $request->has('active') ? $request->boolean('active') : false,
        ]);

        return back()->with('success', 'Announcement updated.');
    }

    public function deleteAnnouncement(int $id)
    {
        $announcement = LibraryAnnouncement::findOrFail($id);
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }

    public function reorderAnnouncements(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:library_announcements,id',
        ]);

        foreach ($request->order as $index => $id) {
            LibraryAnnouncement::where('id', (int) $id)->update(['position' => $index + 1]);
        }

        return response()->json(['status' => 'ok']);
    }
}
