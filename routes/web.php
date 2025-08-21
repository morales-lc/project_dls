

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MidesController;
use App\Http\Controllers\MidesDashboardController;

use App\Http\Controllers\MidesUndergradController;

use App\Http\Controllers\MidesGraduateController;

use App\Http\Controllers\MidesSeniorHighController;
use Illuminate\Http\Request;



Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/resource/view', [ResourceController::class, 'show'])->name('resource.view');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

Route::view('/saved', 'saved')->name('saved');
Route::view('/history', 'history')->name('history');
Route::view('/settings', 'settings')->name('settings');

Route::view('/about', 'about')->name('about');
Route::view('/chart', 'chart')->name('chart');
Route::view('/', 'login')->name('login');

Route::get('/wiley-login', function () {
    return view('wiley-auto-login');
});



Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/profile/complete', function () {
    return view('profile.complete');
})->name('profile.complete')->middleware('auth');

Route::post('/profile/complete', [ProfileController::class, 'completeProfile'])->middleware('auth');

Route::get('/user-management', [UserManagementController::class, 'index'])->name('user.management');



// Mides routes
Route::get('/mides-management', [MidesController::class, 'index'])->name('mides.management');
Route::get('/mides-upload', [MidesController::class, 'create'])->name('mides.upload');
Route::post('/mides-upload', [MidesController::class, 'store'])->name('mides.store');

Route::get('/mides', [MidesDashboardController::class, 'index'])->name('mides.dashboard');
Route::get('/mides-search', [MidesDashboardController::class, 'search'])->name('mides.search');


// Undergraduate Baby Theses menu and program listing
Route::get('/mides/undergrad', [MidesUndergradController::class, 'index'])->name('mides.undergrad');
Route::get('/mides/undergrad/programs', [MidesUndergradController::class, 'programs'])->name('mides.undergrad.programs');
Route::get('/mides/undergrad/{program}', [MidesUndergradController::class, 'program'])->name('mides.undergrad.program');


// Graduate Theses menu and category listing
Route::get('/mides/graduate', [MidesGraduateController::class, 'index'])->name('mides.graduate');
Route::get('/mides/graduate/categories', [MidesGraduateController::class, 'categories'])->name('mides.graduate.categories');
Route::get('/mides/graduate/{category}', [MidesGraduateController::class, 'category'])->name('mides.graduate.category');

// Faculty/Theses/Dissertations listing
Route::get('/mides/faculty-theses', [MidesDashboardController::class, 'facultyTheses'])->name('mides.faculty_theses');

// Senior High School Research Paper routes

Route::get('/mides/seniorhigh/programs', [MidesSeniorHighController::class, 'programs'])->name('mides.seniorhigh.programs');
Route::get('/mides/seniorhigh/{program}', [MidesSeniorHighController::class, 'program'])->name('mides.seniorhigh.program');