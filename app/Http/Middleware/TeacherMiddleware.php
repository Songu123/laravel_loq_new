<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login.teacher')->with('error', 'Vui lòng đăng nhập để truy cập.');
        }

        // Check if user has teacher role
        if (Auth::user()->role !== 'teacher') {
            // Redirect based on user role
            if (Auth::user()->role === 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn đang ở khu vực Admin.');
            } elseif (Auth::user()->role === 'student') {
                return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập khu vực Giáo viên.');
            }
            
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}