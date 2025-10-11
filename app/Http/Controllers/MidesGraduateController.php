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

        // support category as id or name
        if (is_numeric($category)) {
            $query = MidesDocument::where('mides_category_id', $category)->where('type', 'Graduate Theses');
        } else {
            $query = MidesDocument::where('type', 'Graduate Theses')->where(function($q) use ($category) {
                $q->where('category', $category)->orWhereHas('midesCategory', function($q2) use ($category) {
                    $q2->where('name', $category)->where('type', 'Graduate Theses');
                });
            });
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('author', 'like', "%$search%")
                    ->orWhere('year', 'like', "%$search%");
            });
        }
        $documents = $query->orderBy($sort, $direction)->paginate(12)->appends(['search' => $search, 'sort' => $sort, 'direction' => $direction]);
        return view('mides-graduate-list', compact('documents', 'category', 'search', 'sort', 'direction'));
    }

    public function viewer($id)
    {
        $doc = \App\Models\MidesDocument::findOrFail($id);
        return view('mides-pdf-viewer', compact('doc'));
    }

    public function categories()
    {
        $categories = MidesCategory::where('type', 'Graduate Theses')->pluck('name');
        return view('mides-graduate-categories', compact('categories'));
    }
}
