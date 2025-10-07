




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

use App\Http\Controllers\LibraryStaffController;
use App\Http\Controllers\AlertServiceController;
use App\Http\Controllers\AlinetAppointmentManageController;





Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/resource/view', [ResourceController::class, 'show'])->name('resource.view');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

Route::view('/saved', 'saved')->name('saved');
Route::view('/history', 'history')->name('history');
Route::view('/settings', 'settings')->name('settings');

Route::view('/about', 'about')->name('about');
use App\Http\Controllers\ContactController;

use Illuminate\Support\Facades\Auth;
Route::get('/about/contact', [ContactController::class, 'index'])->name('about.contact');

// Admin Contact Info Management
Route::get('/admin/contact-info', [ContactController::class, 'adminContactInfo'])->name('admin.contact-info');
Route::put('/admin/contact-info', [ContactController::class, 'updateContactInfo'])->name('admin.contact-info.update');
Route::view('/chart', 'chart')->name('chart');

Route::get('/', [App\Http\Controllers\PostController::class, 'index'])->name('dashboard');
Route::view('/login', 'login')->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');
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
Route::get('/user-management/create', [UserManagementController::class, 'create'])->name('user.create');
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
// Undergraduate Thesis PDF Viewer (No download/print)
Route::get('/mides/undergrad/viewer/{id}', [MidesUndergradController::class, 'viewer'])->name('mides.undergrad.viewer');


// Graduate Theses menu and category listing
Route::get('/mides/graduate', [MidesGraduateController::class, 'index'])->name('mides.graduate');
Route::get('/mides/graduate/categories', [MidesGraduateController::class, 'categories'])->name('mides.graduate.categories');
Route::get('/mides/graduate/{category}', [MidesGraduateController::class, 'category'])->name('mides.graduate.category');
// Graduate Thesis PDF Viewer (No download/print)
Route::get('/mides/viewer/{id}', [MidesGraduateController::class, 'viewer'])->name('mides.viewer');

// Faculty/Theses/Dissertations listing
Route::get('/mides/faculty-theses', [MidesDashboardController::class, 'facultyTheses'])->name('mides.faculty_theses');

// Senior High School Research Paper routes

Route::get('/mides/seniorhigh/programs', [MidesSeniorHighController::class, 'programs'])->name('mides.seniorhigh.programs');
Route::get('/mides/seniorhigh/{program}', [MidesSeniorHighController::class, 'program'])->name('mides.seniorhigh.program');
Route::get('/mides/seniorhigh/viewer/{id}', [MidesSeniorHighController::class, 'viewer'])->name('mides.seniorhigh.viewer');

// Bookmarks (students/faculty)
Route::middleware('auth')->group(function () {
    Route::get('/bookmarks', [\App\Http\Controllers\BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks/toggle', [\App\Http\Controllers\BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
});


// Admin Category Control Panel
Route::get('/mides-categories-panel', [MidesController::class, 'categoriesPanel'])->name('mides.categories.panel');
Route::post('/mides-categories-panel/add', [MidesController::class, 'addCategory'])->name('mides.categories.add');
Route::put('/mides-categories-panel/{id}', [MidesController::class, 'updateCategory'])->name('mides.categories.update');
Route::delete('/mides-categories-panel/{id}', [MidesController::class, 'deleteCategory'])->name('mides.categories.delete');
// Admin Dashboard
Route::get('/admin-dashboard', function() {
    if (Auth::check() && Auth::user()->role === 'admin') {
        return view('admin-dashboard');
    }
    abort(403);
})->middleware('auth')->name('admin.dashboard');

// Librarian Dashboard
Route::get('/librarian-dashboard', function() {
    if (Auth::check() && in_array(Auth::user()->role, ['librarian'])) {
        return view('librarian.dashboard');
    }
    abort(403);
})->middleware('auth')->name('librarian.dashboard');
// Admin Posts Management
// Admin-only routes group
Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => function ($request, $next) {
        if (Auth::user()->role !== 'admin') abort(403);
        return $next($request);
    }], function () {
        Route::get('/admin-dashboard', function() {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return view('admin-dashboard');
            }
            abort(403);
        })->name('admin.dashboard');
        Route::get('/user-management', [UserManagementController::class, 'index'])->name('user.management');
        Route::get('/libraries/staff/manage', [LibraryStaffController::class, 'manage'])->name('libraries.staff.manage');
        Route::get('/mides-management', [MidesController::class, 'index'])->name('mides.management');
        Route::get('/alert-services/manage', [AlertServiceController::class, 'manage'])->name('alert-services.manage');
        Route::get('/alinet/manage', [AlinetAppointmentManageController::class, 'index'])->name('alinet.manage');
        Route::get('/admin/feedback', [App\Http\Controllers\FeedbackController::class, 'adminList'])->name('feedback.admin');
        Route::get('/post-management', [App\Http\Controllers\PostController::class, 'postManagement'])->name('post.management');
        Route::get('/sidlak/manage', [App\Http\Controllers\SidlakJournalController::class, 'manage'])->name('sidlak.manage');
        Route::get('/mides-categories-panel', [MidesController::class, 'categoriesPanel'])->name('mides.categories.panel');
        Route::get('/admin/contact-info', [ContactController::class, 'adminContactInfo'])->name('admin.contact-info');

    // Admin Profile (My Profile)
    Route::get('/admin/profile', [\App\Http\Controllers\AdminProfileController::class, 'edit'])->name('admin.profile');
    Route::put('/admin/profile', [\App\Http\Controllers\AdminProfileController::class, 'update'])->name('admin.profile.update');

    // Admin Backup Management
    Route::get('/admin/backup', [\App\Http\Controllers\BackupController::class, 'index'])->name('admin.backup');
    Route::post('/admin/backup/run', [\App\Http\Controllers\BackupController::class, 'run'])->name('admin.backup.run');
    Route::get('/admin/backup/download/{file}', [\App\Http\Controllers\BackupController::class, 'download'])->name('admin.backup.download');
    });
});

// Librarian routes: grant access to management modules (same as admin)
Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => function ($request, $next) {
        if (!in_array(Auth::user()->role, ['librarian','admin'])) abort(403);
        return $next($request);
    }], function () {
        Route::get('/librarian/information-literacy/manage', [App\Http\Controllers\InformationLiteracyController::class, 'manage'])->name('information_literacy.manage');
        Route::get('/librarian/post-management', [App\Http\Controllers\PostController::class, 'postManagement'])->name('post.management');
        Route::get('/librarian/sidlak/manage', [App\Http\Controllers\SidlakJournalController::class, 'manage'])->name('sidlak.manage');
        Route::get('/librarian/alinet/manage', [AlinetAppointmentManageController::class, 'index'])->name('alinet.manage');
        Route::get('/librarian/alert-services/manage', [AlertServiceController::class, 'manage'])->name('alert-services.manage');
        Route::get('/librarian/mides-management', [MidesController::class, 'index'])->name('mides.management');
    });
});
Route::get('/admin-posts-management', [App\Http\Controllers\PostController::class, 'adminManagement'])->name('admin.posts.management');
Route::get('/admin-posts-management/{id}/edit', [App\Http\Controllers\PostController::class, 'edit'])->name('admin.posts.edit');
Route::put('/admin-posts-management/{id}', [App\Http\Controllers\PostController::class, 'update'])->name('admin.posts.update');
Route::delete('/admin-posts-management/{id}', [App\Http\Controllers\PostController::class, 'destroy'])->name('admin.posts.delete');



// Post routes
Route::post('/dashboard/post', [App\Http\Controllers\PostController::class, 'store'])->name('dashboard.post.store');

// JSON endpoint for posts (for modal population)
Route::get('/posts/{id}/json', [App\Http\Controllers\PostController::class, 'showJson'])->name('posts.show.json');

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


// ALINET Appointment Management
Route::get('/alinet/manage', [AlinetAppointmentManageController::class, 'index'])->name('alinet.manage');
Route::post('/alinet/{id}/status', [AlinetAppointmentManageController::class, 'updateStatus'])->name('alinet.status');

// Learning Spaces page
Route::get('/learning-spaces', function () {
    return view('learning-spaces');
})->name('learning-spaces');


// LiRA Jotform
use App\Http\Controllers\LiRAController;
Route::get('/lira/form', [LiRAController::class, 'showForm'])->middleware('auth')->name('lira.form');

// LiRA Jotform autofill route
Route::get('/lira/jotform', [App\Http\Controllers\LiRAController::class, 'showForm'])->name('lira.jotform');



// Library department routes
Route::get('/libraries/college', [LibraryStaffController::class, 'index'])->defaults('department', 'college')->name('libraries.college');
Route::get('/libraries/graduate', [LibraryStaffController::class, 'index'])->defaults('department', 'graduate')->name('libraries.graduate');
Route::get('/libraries/senior-high', [LibraryStaffController::class, 'index'])->defaults('department', 'senior_high')->name('libraries.senior_high');
Route::get('/libraries/ibed', [LibraryStaffController::class, 'index'])->defaults('department', 'ibed')->name('libraries.ibed');
Route::get('/libraries/staff/add', [LibraryStaffController::class, 'create'])->name('libraries.staff.create');
Route::post('/libraries/staff/add', [LibraryStaffController::class, 'store'])->name('libraries.staff.store');
// Library Staff Management
Route::get('/libraries/staff/manage', [LibraryStaffController::class, 'manage'])->name('libraries.staff.manage');
Route::get('/libraries/staff/{id}/edit', [LibraryStaffController::class, 'edit'])->name('libraries.staff.edit');
Route::put('/libraries/staff/{id}', [LibraryStaffController::class, 'update'])->name('libraries.staff.update');
Route::delete('/libraries/staff/{id}', [LibraryStaffController::class, 'destroy'])->name('libraries.staff.destroy');

// Feedback routes (only for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/feedback', [App\Http\Controllers\FeedbackController::class, 'showForm'])->name('feedback.form');
    Route::post('/feedback', [App\Http\Controllers\FeedbackController::class, 'submit'])->name('feedback.submit');
});
Route::get('/admin/feedback', [App\Http\Controllers\FeedbackController::class, 'adminList'])->name('feedback.admin');
Route::get('/admin/feedback/{id}/followup', [App\Http\Controllers\FeedbackController::class, 'followUp'])->name('feedback.followup');
// Feedback delete route
Route::delete('/admin/feedback/{id}', [App\Http\Controllers\FeedbackController::class, 'delete'])->name('feedback.delete');

// Online E-Libraries page
Route::get('/elibraries', function () {
    return view('elibraries');
})->name('elibraries');


// Information Literacy routes
use App\Http\Controllers\InformationLiteracyController;
Route::get('/information-literacy', [InformationLiteracyController::class, 'index'])->name('information_literacy.index');
Route::get('/information-literacy/create', [InformationLiteracyController::class, 'create'])->name('information_literacy.create');
Route::post('/information-literacy/store', [InformationLiteracyController::class, 'store'])->name('information_literacy.store');
Route::get('/information-literacy/manage', function() {
    $posts = \App\Models\InformationLiteracyPost::orderBy('date_time', 'desc')->get();
    return view('information_literacy.manage', compact('posts'));
})->name('information_literacy.manage');
Route::get('/information-literacy/{id}/edit', [InformationLiteracyController::class, 'edit'])->name('information_literacy.edit');
Route::put('/information-literacy/{id}/update', [InformationLiteracyController::class, 'update'])->name('information_literacy.update');
Route::delete('/information-literacy/{id}/delete', [InformationLiteracyController::class, 'destroy'])->name('information_literacy.delete');
