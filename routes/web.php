<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes(['verify' => true]);

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home');

Route::middleware(('guest'))->group(function () {
    // General auth routes
    Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register.post');

    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
    
    // Admin registration and login
    Route::get('/register/admin', [\App\Http\Controllers\AuthController::class, 'showRegisterAdmin'])->name('register.admin');
    Route::post('/register/admin', [\App\Http\Controllers\AuthController::class, 'registerAdmin'])->name('register.admin.post');
    Route::get('/login/admin', [\App\Http\Controllers\AuthController::class, 'showLoginAdmin'])->name('login.admin');
    Route::post('/login/admin', [\App\Http\Controllers\AuthController::class, 'loginAdmin'])->name('login.admin.post');
    
    // Teacher registration and login
    Route::get('/register/teacher', [\App\Http\Controllers\AuthController::class, 'showRegisterTeacher'])->name('register.teacher');
    Route::post('/register/teacher', [\App\Http\Controllers\AuthController::class, 'registerTeacher'])->name('register.teacher.post');
    Route::get('/login/teacher', [\App\Http\Controllers\AuthController::class, 'showLoginTeacher'])->name('login.teacher');
    Route::post('/login/teacher', [\App\Http\Controllers\AuthController::class, 'loginTeacher'])->name('login.teacher.post');
    
    // Student registration and login
    Route::get('/register/student', [\App\Http\Controllers\AuthController::class, 'showRegisterStudent'])->name('register.student');
    Route::post('/register/student', [\App\Http\Controllers\AuthController::class, 'registerStudent'])->name('register.student.post');
    Route::get('/login/student', [\App\Http\Controllers\AuthController::class, 'showLoginStudent'])->name('login.student');
    Route::post('/login/student', [\App\Http\Controllers\AuthController::class, 'loginStudent'])->name('login.student.post');
});

// HomeController routes
Route::get('/home-controller', [\App\Http\Controllers\HomeController::class, 'index'])->name('home.index');
Route::get('/about', [\App\Http\Controllers\HomeController::class, 'about'])->name('home.about');
Route::get('/contact', [\App\Http\Controllers\HomeController::class, 'contact'])->name('home.contact');

// logout
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Email verification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/home');
    })->middleware(['signed'])->name('verification.verify');
    
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Auth::routes(); // Đã tắt xác thực tự động để /home không bị override

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Category management routes
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::post('categories/{category}/toggle-status', [\App\Http\Controllers\CategoryController::class, 'toggleStatus'])
        ->name('categories.toggle-status');
});

// Teacher routes
Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', function () {
        return view('teacher-dashboard');
    })->name('dashboard');
    
    // Teacher category routes (CRUD with restrictions)
    Route::resource('categories', \App\Http\Controllers\Teacher\CategoryController::class);
    Route::post('categories/{category}/request-approval', [\App\Http\Controllers\Teacher\CategoryController::class, 'requestApproval'])
        ->name('categories.request-approval');
    Route::get('api/categories', [\App\Http\Controllers\Teacher\CategoryController::class, 'getCategories'])->name('categories.api');
    
    // Teacher exam routes (CRUD with ownership restrictions)
    Route::resource('exams', \App\Http\Controllers\Teacher\ExamController::class);
    Route::post('exams/{exam}/toggle-status', [\App\Http\Controllers\Teacher\ExamController::class, 'toggleStatus'])
        ->name('exams.toggle-status');
    Route::post('exams/{exam}/duplicate', [\App\Http\Controllers\Teacher\ExamController::class, 'duplicate'])
        ->name('exams.duplicate');
});

// Student routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Student\StudentController::class, 'dashboard'])->name('dashboard');
    
    // Exams - Browse, View, Take
    Route::get('/exams', [\App\Http\Controllers\Student\StudentController::class, 'exams'])->name('exams.index');
    Route::get('/exams/{exam}', [\App\Http\Controllers\Student\StudentController::class, 'show'])->name('exams.show');
    Route::get('/exams/{exam}/take', [\App\Http\Controllers\Student\StudentController::class, 'take'])->name('exams.take');
    Route::post('/exams/{exam}/submit', [\App\Http\Controllers\Student\StudentController::class, 'submit'])->name('exams.submit');
    
    // History & Results
    Route::get('/history', [\App\Http\Controllers\Student\StudentController::class, 'history'])->name('history');
    Route::get('/results/{attempt}', [\App\Http\Controllers\Student\StudentController::class, 'resultDetail'])->name('results.show');
});


// Backward compatibility for old dashboard route
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    });
});


Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
