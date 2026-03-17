<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LibrarySlideshowImage;

class HomeController extends Controller
{
    /**
     * Display the dashboard view.
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Display the about page with slideshow images.
     */
    public function about()
    {
        $slideshowImages = LibrarySlideshowImage::active()->get();
        return view('about', compact('slideshowImages'));
    }
}

