<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MidesDocument;
use App\Models\MidesCategory;

class MidesGraduateController extends Controller
{
    public function index()
    {
        $categories = MidesCategory::where('type', 'Graduate Theses')->pluck('name');
        return view('mides-graduate-categories', compact('categories'));
    }

    public function category($category)
    {
        $documents = MidesDocument::where('type', 'Graduate Theses')
            ->where('category', $category)
            ->orderBy('year', 'desc')
            ->paginate(12);
        return view('mides-graduate-list', compact('documents', 'category'));
    }

    public function categories()
    {
        $categories = MidesCategory::where('type', 'Graduate Theses')->pluck('name');
        return view('mides-graduate-categories', compact('categories'));
    }
}
