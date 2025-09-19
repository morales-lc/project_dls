



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

use App\Http\Controllers\LoginController;





Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/resource/view', [ResourceController::class, 'show'])->name('resource.view');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

Route::view('/saved', 'saved')->name('saved');
Route::view('/history', 'history')->name('history');
Route::view('/settings', 'settings')->name('settings');

Route::view('/about', 'about')->name('about');
Route::view('/chart', 'chart')->name('chart');

Route::get('/', [App\Http\Controllers\PostController::class, 'index'])->name('dashboard');
Route::view('/login', 'login')->name('login');
// Logout route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

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

// Sidlak Journal routes
use App\Http\Controllers\SidlakJournalController;
Route::get('/sidlak-journals', [SidlakJournalController::class, 'index'])->name('sidlak.index');
Route::get('/sidlak-journals/create', [SidlakJournalController::class, 'create'])->name('sidlak.create');
Route::post('/sidlak-journals', [SidlakJournalController::class, 'store'])->name('sidlak.store');
Route::get('/sidlak-journals/{id}', [SidlakJournalController::class, 'show'])->name('sidlak.show');
// Sidlak Journal CRUD Management
Route::get('/sidlak/manage', [\App\Http\Controllers\SidlakJournalController::class, 'manage'])->name('sidlak.manage');
Route::get('/sidlak/{id}/edit', [\App\Http\Controllers\SidlakJournalController::class, 'edit'])->name('sidlak.edit');
Route::put('/sidlak/{id}', [\App\Http\Controllers\SidlakJournalController::class, 'update'])->name('sidlak.update');
Route::delete('/sidlak/{id}', [\App\Http\Controllers\SidlakJournalController::class, 'destroy'])->name('sidlak.destroy');


// Alert Services routes
use App\Http\Controllers\AlertServiceController;
Route::get('/alert-services', [AlertServiceController::class, 'index'])->name('alert-services.index');
Route::get('/alert-services/manage', [AlertServiceController::class, 'manage'])->name('alert-services.manage');
Route::get('/alert-services/create', [AlertServiceController::class, 'create'])->name('alert-services.create');
Route::post('/alert-services', [AlertServiceController::class, 'store'])->name('alert-services.store');
Route::get('/alert-services/{id}/edit', [AlertServiceController::class, 'edit'])->name('alert-services.edit');
Route::put('/alert-services/{id}', [AlertServiceController::class, 'update'])->name('alert-services.update');
Route::delete('/alert-services/{id}', [AlertServiceController::class, 'destroy'])->name('alert-services.destroy');
Route::get('/alert-services/{year}/{month}/{group}/{value}', [AlertServiceController::class, 'group'])->name('alert-services.group');

use App\Http\Controllers\AlinetController;
// ALINET Appointment
Route::get('/alinet', [AlinetController::class, 'showForm'])->name('alinet.form');
Route::post('/alinet', [AlinetController::class, 'submitForm'])->name('alinet.submit');

use App\Http\Controllers\AlinetAppointmentManageController;
// ALINET Appointment Management
Route::get('/alinet/manage', [AlinetAppointmentManageController::class, 'index'])->name('alinet.manage');
Route::post('/alinet/{id}/status', [AlinetAppointmentManageController::class, 'updateStatus'])->name('alinet.status');

// Learning Spaces page
Route::get('/learning-spaces', function () {
    return view('learning-spaces');
})->name('learning-spaces');
