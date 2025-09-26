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
}
