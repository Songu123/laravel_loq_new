<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\SocialAuthController;
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

// Social Authentication Routes (Google, Facebook)
Route::middleware('guest')->group(function () {
    // Google OAuth
    Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    
    // Facebook OAuth (optional - for future)
    Route::get('auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback'])->name('auth.facebook.callback');
});

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
    Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])
        ->name('dashboard');
    
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
    
    // Violation Management
    Route::get('/violations/flagged', [\App\Http\Controllers\ViolationAnalysisController::class, 'flaggedAttempts'])
        ->name('violations.flagged');
    Route::get('/violations/report/{attempt}', [\App\Http\Controllers\ViolationAnalysisController::class, 'violationReport'])
        ->name('violations.report');
    Route::get('/violations/analysis', [\App\Http\Controllers\ViolationAnalysisController::class, 'analyzePattern'])
        ->name('violations.analysis');
    // Classes for teachers
    Route::resource('classes', \App\Http\Controllers\Teacher\ClassController::class)->only(['index','create','store','show']);
    Route::post('classes/{class}/regenerate-code', [\App\Http\Controllers\Teacher\ClassController::class, 'regenerateCode'])
        ->name('classes.regenerate-code');
    Route::post('classes/{class}/toggle-approval', [\App\Http\Controllers\Teacher\ClassController::class, 'toggleApproval'])
        ->name('classes.toggle-approval');
    Route::post('classes/{class}/requests/{requestItem}/approve', [\App\Http\Controllers\Teacher\ClassController::class, 'approveRequest'])
        ->name('classes.requests.approve');
    Route::post('classes/{class}/requests/{requestItem}/reject', [\App\Http\Controllers\Teacher\ClassController::class, 'rejectRequest'])
        ->name('classes.requests.reject');
    Route::post('classes/{class}/remove-student', [\App\Http\Controllers\Teacher\ClassController::class, 'removeStudent'])
        ->name('classes.remove-student');
    Route::post('classes/{class}/attach-exam', [\App\Http\Controllers\Teacher\ClassController::class, 'attachExam'])
        ->name('classes.attach-exam');
    Route::post('classes/{class}/detach-exam', [\App\Http\Controllers\Teacher\ClassController::class, 'detachExam'])
        ->name('classes.detach-exam');
    Route::get('classes/{class}/exams/{exam}/results', [\App\Http\Controllers\Teacher\ClassController::class, 'examResults'])
        ->name('classes.exam-results');
    
    // AI Question Import from PDF
    Route::get('ai-import', [\App\Http\Controllers\Teacher\AIQuestionImportController::class, 'showUploadForm'])
        ->name('ai-import.form');
    Route::post('ai-import/upload', [\App\Http\Controllers\Teacher\AIQuestionImportController::class, 'uploadPDF'])
        ->name('ai-import.upload');
    Route::get('ai-import/review', [\App\Http\Controllers\Teacher\AIQuestionImportController::class, 'reviewQuestions'])
        ->name('ai-import.review');
    Route::post('ai-import/save', [\App\Http\Controllers\Teacher\AIQuestionImportController::class, 'saveQuestions'])
        ->name('ai-import.save');
    Route::post('ai-import/cancel', [\App\Http\Controllers\Teacher\AIQuestionImportController::class, 'cancelImport'])
        ->name('ai-import.cancel');
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
    
    // Anti-Cheating - Violation Logging
    Route::post('/exams/log-violation', [\App\Http\Controllers\ViolationAnalysisController::class, 'logViolation'])
        ->name('exams.log-violation');
    Route::get('/violations/{attempt}', [\App\Http\Controllers\ViolationAnalysisController::class, 'getAttemptViolations'])
        ->name('violations.attempt');
    
    // History & Results
    Route::get('/history', [\App\Http\Controllers\Student\StudentController::class, 'history'])->name('history');
    Route::get('/results/{attempt}', [\App\Http\Controllers\Student\StudentController::class, 'resultDetail'])->name('results.show');
    
    // Practice System
    Route::get('/practice', [\App\Http\Controllers\Student\PracticeController::class, 'index'])->name('practice.index');
    Route::get('/practice/wrong-answers', [\App\Http\Controllers\Student\PracticeController::class, 'wrongAnswers'])->name('practice.wrong-answers');
    Route::get('/practice/start', [\App\Http\Controllers\Student\PracticeController::class, 'startPractice'])->name('practice.start');
    Route::post('/practice/submit', [\App\Http\Controllers\Student\PracticeController::class, 'submitPractice'])->name('practice.submit');

    // Notifications - All
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'page'])->name('notifications');

    // Classes - Student join and list
    Route::get('/classes', [\App\Http\Controllers\Student\ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/join', [\App\Http\Controllers\Student\ClassController::class, 'joinForm'])->name('classes.join');
    Route::post('/classes/join', [\App\Http\Controllers\Student\ClassController::class, 'join'])->name('classes.join.post');
    Route::get('/classes/{class}', [\App\Http\Controllers\Student\ClassController::class, 'show'])->name('classes.show');
});

// Authenticated user notifications (JSON via web routes for Blade fetch)
Route::middleware(['auth'])->prefix('user/notifications')->name('user.notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/unread', [\App\Http\Controllers\NotificationController::class, 'unread'])->name('unread');
    Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('readAll');
});


// Backward compatibility for old dashboard route
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    });
});
