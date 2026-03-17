<?php

namespace App\Http\Controllers;

use App\Models\ContactInfo;
use App\Models\LibraryStaff;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contact = ContactInfo::first();
        $libraryStaff = LibraryStaff::orderBy('department')->orderBy('first_name')->get();
        return view('contact', compact('contact', 'libraryStaff'));
    }

        // Admin view for managing contact info
        public function adminContactInfo()
        {
            $contact = ContactInfo::first();
            return view('admin-contact-info', compact('contact'));
        }

        // Update contact info logic
        public function updateContactInfo(Request $request)
        {
            $contact = ContactInfo::first();
            if (!$contact) {
                $contact = new ContactInfo();
            }
            $contact->fill($request->only([
                'phone_college',
                'phone_graduate',
                'phone_senior_high',
                'phone_ibed',
                'facebook_url',
                'email',
                'website_url',
            ]));
            $contact->save();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Contact info updated successfully.']);
            }
            return redirect()->route('library.content.manage')->with('success', 'Contact info updated successfully.');
        }
}
