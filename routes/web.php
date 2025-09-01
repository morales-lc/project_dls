



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



Route::get('/dashboard', [App\Http\Controllers\PostController::class, 'index'])->name('dashboard');
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
Route::post('/user-management/add', [UserManagementController::class, 'add'])->name('user.add');
Route::put('/user-management/{id}', [UserManagementController::class, 'update'])->name('user.update');
Route::delete('/user-management/{id}', [UserManagementController::class, 'delete'])->name('user.delete');



// Mides routes
Route::get('/mides-management', [MidesController::class, 'index'])->name('mides.management');
Route::get('/mides-upload', [MidesController::class, 'create'])->name('mides.upload');
Route::post('/mides-upload', [MidesController::class, 'store'])->name('mides.store');
Route::put('/mides-management/{id}', [MidesController::class, 'update'])->name('mides.update');
Route::delete('/mides-management/{id}', [MidesController::class, 'destroy'])->name('mides.delete');

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

// Admin Category Control Panel
Route::get('/mides-categories-panel', [MidesController::class, 'categoriesPanel'])->name('mides.categories.panel');
Route::post('/mides-categories-panel/add', [MidesController::class, 'addCategory'])->name('mides.categories.add');
Route::put('/mides-categories-panel/{id}', [MidesController::class, 'updateCategory'])->name('mides.categories.update');
Route::delete('/mides-categories-panel/{id}', [MidesController::class, 'deleteCategory'])->name('mides.categories.delete');
// Admin Dashboard
Route::get('/admin-dashboard', function() { return view('admin-dashboard'); })->name('admin.dashboard');

// Librarian Dashboard
Route::get('/librarian-dashboard', function() { return view('librarian-dashboard'); })->name('librarian.dashboard');
// Admin Posts Management
Route::get('/admin-posts-management', [App\Http\Controllers\PostController::class, 'adminManagement'])->name('admin.posts.management');
Route::get('/admin-posts-management/{id}/edit', [App\Http\Controllers\PostController::class, 'edit'])->name('admin.posts.edit');
Route::put('/admin-posts-management/{id}', [App\Http\Controllers\PostController::class, 'update'])->name('admin.posts.update');
Route::delete('/admin-posts-management/{id}', [App\Http\Controllers\PostController::class, 'destroy'])->name('admin.posts.delete');



// Post routes
Route::post('/dashboard/post', [App\Http\Controllers\PostController::class, 'store'])->name('dashboard.post.store');

// Post Management routes
Route::get('/post-management/{id}/edit', [App\Http\Controllers\PostController::class, 'edit'])->name('post.edit');
Route::put('/post-management/{id}', [App\Http\Controllers\PostController::class, 'update'])->name('post.update');
Route::delete('/post-management/{id}', [App\Http\Controllers\PostController::class, 'destroy'])->name('post.delete');
Route::get('/post-management', [App\Http\Controllers\PostController::class, 'postManagement'])->name('post.management');
