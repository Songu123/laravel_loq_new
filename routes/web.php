<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;


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
    Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register.post');

    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
});

// Đăng nhập cho quản trị viên/giảng viên
Route::get('/login/admin', function () {
    return view('auth.login_admin');
})->name('login.admin');
Route::post('/login/admin', [\App\Http\Controllers\AuthController::class, 'loginAdmin'])->name('login.admin.post');

// Đăng nhập cho học sinh/sinh viên
Route::get('/login/student', function () {
    return view('auth.login_student');
})->name('login.student');
Route::post('/login/student', [\App\Http\Controllers\AuthController::class, 'loginStudent'])->name('login.student.post');

// HomeController routes
Route::get('/home-controller', [\App\Http\Controllers\HomeController::class, 'index'])->name('home.index');
Route::get('/about', [\App\Http\Controllers\HomeController::class, 'about'])->name('home.about');
Route::get('/contact', [\App\Http\Controllers\HomeController::class, 'contact'])->name('home.contact');

// logout
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
// Auth::routes(); // Đã tắt xác thực tự động để /home không bị override

// Dashboard demo route (chỉ cho user đã đăng nhập)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
