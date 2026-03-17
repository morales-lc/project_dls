<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\BookBorrowingSettings;
use App\Models\ScanningServiceSettings;

class BookBorrowingController extends Controller
{
    public function show()
    {
        $settings = BookBorrowingSettings::get();
        return view('book-borrowing', compact('settings'));
    }

    public function scanningServices()
    {
        $settings = ScanningServiceSettings::get();
        return view('scanning-services', compact('settings'));
    }

    public function netzone()
    {
        return view('netzone');
    }
}
