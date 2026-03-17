<?php

namespace App\Http\Controllers;

use App\Models\NetzoneSettings;
use Illuminate\Http\Request;

class NetzoneController extends Controller
{
    public function show()
    {
        $settings = NetzoneSettings::get();
        return view('netzone', compact('settings'));
    }
}
