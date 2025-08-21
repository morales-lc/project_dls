<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentFaculty;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = StudentFaculty::with('user')->get();
        return view('user-management', compact('users'));
    }
}
