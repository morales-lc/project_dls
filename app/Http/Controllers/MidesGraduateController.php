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
        $search = request('search');
        $sort = request('sort', 'year');
        $direction = request('direction', 'desc');

        $query = MidesDocument::where('type', 'Graduate Theses')->where('category', $category);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('author', 'like', "%$search%")
                  ->orWhere('year', 'like', "%$search%");
            });
        }
        $documents = $query->orderBy($sort, $direction)->paginate(12)->appends(['search' => $search, 'sort' => $sort, 'direction' => $direction]);
        return view('mides-graduate-list', compact('documents', 'category', 'search', 'sort', 'direction'));
    }

    public function categories()
    {
        $categories = MidesCategory::where('type', 'Graduate Theses')->pluck('name');
        return view('mides-graduate-categories', compact('categories'));
    }
}
