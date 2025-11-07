<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\LibrarySetting;
use App\Models\LibraryAnnouncement;
use App\Models\ContactInfo;
use App\Models\LibrarySlideshowImage;
use App\Models\NetzoneSettings;
use App\Models\LearningSpaceSettings;
use App\Models\BookBorrowingSettings;
use App\Models\ScanningServiceSettings;

class LibraryContentController extends Controller
{
    public function manage()
    {
        $settings = LibrarySetting::singleton();
        $announcements = LibraryAnnouncement::orderBy('position')->get();
        $contact = ContactInfo::first();
        $slideshowImages = LibrarySlideshowImage::ordered()->get();
        $netzoneSettings = NetzoneSettings::get();
        $learningSpaceSettings = LearningSpaceSettings::get();
        $bookBorrowingSettings = BookBorrowingSettings::get();
        $scanningServiceSettings = ScanningServiceSettings::get();
        
        return view('library-content-management', compact(
            'settings', 
            'announcements', 
            'contact', 
            'slideshowImages',
            'netzoneSettings',
            'learningSpaceSettings',
            'bookBorrowingSettings',
            'scanningServiceSettings'
        ));
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Library hours GIF updated.']);
        }
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Announcement added.']);
        }
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Announcement updated.']);
        }
        return back()->with('success', 'Announcement updated.');
    }

    public function deleteAnnouncement(int $id)
    {
        $announcement = LibraryAnnouncement::findOrFail($id);
        $announcement->delete();
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'Announcement deleted.']);
        }
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

    // Slideshow Image Methods
    public function storeSlideshowImage(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,gif|max:5120',
            'caption' => 'nullable|string|max:255',
            'active' => 'sometimes|boolean',
        ]);

        $path = $request->file('image')->store('slideshow', 'public');
        
        $maxPos = LibrarySlideshowImage::max('position') ?? 0;
        
        LibrarySlideshowImage::create([
            'image_path' => $path,
            'caption' => $request->input('caption'),
            'position' => $maxPos + 1,
            'active' => $request->boolean('active', true),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Slideshow image added.']);
        }
        return back()->with('success', 'Slideshow image added.');
    }

    public function updateSlideshowImage(Request $request, int $id)
    {
        $image = LibrarySlideshowImage::findOrFail($id);
        
        $request->validate([
            'caption' => 'nullable|string|max:255',
            'active' => 'sometimes|boolean',
        ]);

        $image->update([
            'caption' => $request->input('caption'),
            'active' => $request->has('active') ? $request->boolean('active') : false,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Slideshow image updated.']);
        }
        return back()->with('success', 'Slideshow image updated.');
    }

    public function deleteSlideshowImage(int $id)
    {
        $image = LibrarySlideshowImage::findOrFail($id);
        
        // Delete the file from storage
        if ($image->image_path) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $image->delete();
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'Slideshow image deleted.']);
        }
        return back()->with('success', 'Slideshow image deleted.');
    }

    public function reorderSlideshowImages(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:library_slideshow_images,id',
        ]);

        foreach ($request->order as $index => $id) {
            LibrarySlideshowImage::where('id', (int) $id)->update(['position' => $index + 1]);
        }

        return response()->json(['status' => 'ok']);
    }

    // Netzone Management Methods
    public function updateNetzone(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $settings = NetzoneSettings::get();
        $settings->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Netzone settings updated.']);
        }
        return back()->with('success', 'Netzone settings updated.');
    }

    public function addNetzoneImage(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        $settings = NetzoneSettings::get();
        $path = $request->file('image')->store('netzone', 'public');
        
        $images = $settings->images ?? [];
        $images[] = $path;
        $settings->images = $images;
        $settings->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Netzone image added.']);
        }
        return back()->with('success', 'Netzone image added.');
    }

    public function deleteNetzoneImage(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $settings = NetzoneSettings::get();
        $images = $settings->images ?? [];
        
        if (isset($images[$request->index])) {
            Storage::disk('public')->delete($images[$request->index]);
            unset($images[$request->index]);
            $settings->images = array_values($images);
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Netzone image deleted.']);
        }
        return back()->with('success', 'Netzone image deleted.');
    }

    public function updateNetzoneReminder(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
            'text' => 'required|string',
            'type' => 'required|in:danger,warning,info',
        ]);

        $settings = NetzoneSettings::get();
        $reminders = $settings->reminders ?? [];
        
        if (isset($reminders[$request->index])) {
            $reminders[$request->index] = [
                'text' => $request->text,
                'type' => $request->type,
            ];
            $settings->reminders = $reminders;
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Netzone reminder updated.']);
        }
        return back()->with('success', 'Netzone reminder updated.');
    }

    public function addNetzoneReminder(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'type' => 'required|in:danger,warning,info',
        ]);

        $settings = NetzoneSettings::get();
        $reminders = $settings->reminders ?? [];
        
        $reminders[] = [
            'text' => $request->text,
            'type' => $request->type,
        ];
        
        $settings->reminders = $reminders;
        $settings->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Netzone reminder added.']);
        }
        return back()->with('success', 'Netzone reminder added.');
    }

    public function deleteNetzoneReminder(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $settings = NetzoneSettings::get();
        $reminders = $settings->reminders ?? [];
        
        if (isset($reminders[$request->index])) {
            unset($reminders[$request->index]);
            $settings->reminders = array_values($reminders);
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Netzone reminder deleted.']);
        }
        return back()->with('success', 'Netzone reminder deleted.');
    }

    // Learning Space Management Methods
    public function updateLearningSpace(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $settings = LearningSpaceSettings::get();
        $settings->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Learning Space settings updated.']);
        }
        return back()->with('success', 'Learning Space settings updated.');
    }

    public function addLearningSpaceImage(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        $settings = LearningSpaceSettings::get();
        $path = $request->file('image')->store('learning-spaces', 'public');
        
        $images = $settings->images ?? [];
        $images[] = $path;
        $settings->images = $images;
        $settings->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Learning Space image added.']);
        }
        return back()->with('success', 'Learning Space image added.');
    }

    public function deleteLearningSpaceImage(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $settings = LearningSpaceSettings::get();
        $images = $settings->images ?? [];
        
        if (isset($images[$request->index])) {
            Storage::disk('public')->delete($images[$request->index]);
            unset($images[$request->index]);
            $settings->images = array_values($images);
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Learning Space image deleted.']);
        }
        return back()->with('success', 'Learning Space image deleted.');
    }

    public function updateLearningSpaceContent(Request $request)
    {
        $request->validate([
            'content_sections' => 'required|json',
        ]);

        $settings = LearningSpaceSettings::get();
        $settings->content_sections = json_decode($request->content_sections, true);
        $settings->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Learning Space content updated.']);
        }
        return back()->with('success', 'Learning Space content updated.');
    }

    public function addLearningSpaceSection(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'type' => 'required|in:list,numbered',
        ]);

        $settings = LearningSpaceSettings::get();
        $sections = $settings->content_sections ?? [];
        
        $sections[] = [
            'heading' => $request->heading,
            'type' => $request->type,
            'items' => [],
        ];
        
        $settings->content_sections = $sections;
        $settings->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Content section added.']);
        }
        return back()->with('success', 'Content section added.');
    }

    public function updateLearningSpaceSection(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
            'heading' => 'required|string|max:255',
            'type' => 'required|in:list,numbered',
        ]);

        $settings = LearningSpaceSettings::get();
        $sections = $settings->content_sections ?? [];
        
        if (isset($sections[$request->index])) {
            $sections[$request->index]['heading'] = $request->heading;
            $sections[$request->index]['type'] = $request->type;
            $settings->content_sections = $sections;
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Content section updated.']);
        }
        return back()->with('success', 'Content section updated.');
    }

    public function deleteLearningSpaceSection(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $settings = LearningSpaceSettings::get();
        $sections = $settings->content_sections ?? [];
        
        if (isset($sections[$request->index])) {
            unset($sections[$request->index]);
            $settings->content_sections = array_values($sections);
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Content section deleted.']);
        }
        return back()->with('success', 'Content section deleted.');
    }

    public function addLearningSpaceSectionItem(Request $request)
    {
        $request->validate([
            'section_index' => 'required|integer',
            'item_text' => 'required|string',
        ]);

        $settings = LearningSpaceSettings::get();
        $sections = $settings->content_sections ?? [];
        
        if (isset($sections[$request->section_index])) {
            $sections[$request->section_index]['items'][] = $request->item_text;
            $settings->content_sections = $sections;
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Item added to section.']);
        }
        return back()->with('success', 'Item added to section.');
    }

    public function updateLearningSpaceSectionItem(Request $request)
    {
        $request->validate([
            'section_index' => 'required|integer',
            'item_index' => 'required|integer',
            'item_text' => 'required|string',
        ]);

        $settings = LearningSpaceSettings::get();
        $sections = $settings->content_sections ?? [];
        
        if (isset($sections[$request->section_index]['items'][$request->item_index])) {
            $sections[$request->section_index]['items'][$request->item_index] = $request->item_text;
            $settings->content_sections = $sections;
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Item updated.']);
        }
        return back()->with('success', 'Item updated.');
    }

    public function deleteLearningSpaceSectionItem(Request $request)
    {
        $request->validate([
            'section_index' => 'required|integer',
            'item_index' => 'required|integer',
        ]);

        $settings = LearningSpaceSettings::get();
        $sections = $settings->content_sections ?? [];
        
        if (isset($sections[$request->section_index]['items'][$request->item_index])) {
            unset($sections[$request->section_index]['items'][$request->item_index]);
            $sections[$request->section_index]['items'] = array_values($sections[$request->section_index]['items']);
            $settings->content_sections = $sections;
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Item deleted.']);
        }
        return back()->with('success', 'Item deleted.');
    }

    // Book Borrowing Management Methods
    public function updateBookBorrowing(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $settings = BookBorrowingSettings::get();
        $settings->update([
            'title' => $request->title,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Book Borrowing settings updated.']);
        }
        return back()->with('success', 'Book Borrowing settings updated.');
    }

    public function addBookBorrowingImage(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        $settings = BookBorrowingSettings::get();
        $path = $request->file('image')->store('book-borrowing', 'public');
        
        $images = $settings->images ?? [];
        $images[] = $path;
        $settings->images = $images;
        $settings->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Book Borrowing image added.']);
        }
        return back()->with('success', 'Book Borrowing image added.');
    }

    public function deleteBookBorrowingImage(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $settings = BookBorrowingSettings::get();
        $images = $settings->images ?? [];
        
        if (isset($images[$request->index])) {
            Storage::disk('public')->delete($images[$request->index]);
            unset($images[$request->index]);
            $settings->images = array_values($images);
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Book Borrowing image deleted.']);
        }
        return back()->with('success', 'Book Borrowing image deleted.');
    }

    public function addBookBorrowingStep(Request $request)
    {
        $request->validate([
            'step' => 'required|string',
            'type' => 'required|in:borrowing,returning',
        ]);

        $settings = BookBorrowingSettings::get();
        $type = $request->type;
        $steps = $type === 'borrowing' ? ($settings->borrowing_steps ?? []) : ($settings->returning_steps ?? []);
        
        $steps[] = $request->step;
        
        if ($type === 'borrowing') {
            $settings->borrowing_steps = $steps;
        } else {
            $settings->returning_steps = $steps;
        }
        $settings->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Step added.']);
        }
        return back()->with('success', 'Step added.');
    }

    public function updateBookBorrowingStep(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
            'step' => 'required|string',
            'type' => 'required|in:borrowing,returning',
        ]);

        $settings = BookBorrowingSettings::get();
        $type = $request->type;
        $steps = $type === 'borrowing' ? ($settings->borrowing_steps ?? []) : ($settings->returning_steps ?? []);
        
        if (isset($steps[$request->index])) {
            $steps[$request->index] = $request->step;
            if ($type === 'borrowing') {
                $settings->borrowing_steps = $steps;
            } else {
                $settings->returning_steps = $steps;
            }
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Step updated.']);
        }
        return back()->with('success', 'Step updated.');
    }

    public function deleteBookBorrowingStep(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
            'type' => 'required|in:borrowing,returning',
        ]);

        $settings = BookBorrowingSettings::get();
        $type = $request->type;
        $steps = $type === 'borrowing' ? ($settings->borrowing_steps ?? []) : ($settings->returning_steps ?? []);
        
        if (isset($steps[$request->index])) {
            unset($steps[$request->index]);
            $steps = array_values($steps);
            if ($type === 'borrowing') {
                $settings->borrowing_steps = $steps;
            } else {
                $settings->returning_steps = $steps;
            }
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Step deleted.']);
        }
        return back()->with('success', 'Step deleted.');
    }

    // Scanning Service Management Methods
    public function updateScanningService(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'important_note' => 'nullable|string',
            'extract_limits' => 'nullable|string',
        ]);

        $settings = ScanningServiceSettings::get();
        $settings->update([
            'title' => $request->title,
            'important_note' => $request->important_note,
            'extract_limits' => $request->extract_limits,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Scanning Service settings updated.']);
        }
        return back()->with('success', 'Scanning Service settings updated.');
    }

    public function addScanningServiceImage(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        $settings = ScanningServiceSettings::get();
        $path = $request->file('image')->store('scanning-service', 'public');
        
        $images = $settings->images ?? [];
        $images[] = $path;
        $settings->images = $images;
        $settings->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Scanning Service image added.']);
        }
        return back()->with('success', 'Scanning Service image added.');
    }

    public function deleteScanningServiceImage(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $settings = ScanningServiceSettings::get();
        $images = $settings->images ?? [];
        
        if (isset($images[$request->index])) {
            Storage::disk('public')->delete($images[$request->index]);
            unset($images[$request->index]);
            $settings->images = array_values($images);
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Scanning Service image deleted.']);
        }
        return back()->with('success', 'Scanning Service image deleted.');
    }

    public function addScanningServiceStep(Request $request)
    {
        $request->validate([
            'step' => 'required|string',
        ]);

        $settings = ScanningServiceSettings::get();
        $steps = $settings->steps ?? [];
        
        $steps[] = $request->step;
        $settings->steps = $steps;
        $settings->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Step added.']);
        }
        return back()->with('success', 'Step added.');
    }

    public function updateScanningServiceStep(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
            'step' => 'required|string',
        ]);

        $settings = ScanningServiceSettings::get();
        $steps = $settings->steps ?? [];
        
        if (isset($steps[$request->index])) {
            $steps[$request->index] = $request->step;
            $settings->steps = $steps;
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Step updated.']);
        }
        return back()->with('success', 'Step updated.');
    }

    public function deleteScanningServiceStep(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $settings = ScanningServiceSettings::get();
        $steps = $settings->steps ?? [];
        
        if (isset($steps[$request->index])) {
            unset($steps[$request->index]);
            $settings->steps = array_values($steps);
            $settings->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Step deleted.']);
        }
        return back()->with('success', 'Step deleted.');
    }
}
