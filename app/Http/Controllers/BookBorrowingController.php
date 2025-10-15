<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class BookBorrowingController extends Controller
{
    public function show()
    {
        return view('book-borrowing');
    }

    public function scanningServices()
    {
        return view('scanning-services');
    }

    public function netzone()
    {
        return view('netzone');
    }
}
