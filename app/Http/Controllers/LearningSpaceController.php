<?php

namespace App\Http\Controllers;

use App\Models\LearningSpaceSettings;
use Illuminate\Http\Request;

class LearningSpaceController extends Controller
{
    public function show()
    {
        $settings = LearningSpaceSettings::get();
        return view('learning-spaces', compact('settings'));
    }
}
