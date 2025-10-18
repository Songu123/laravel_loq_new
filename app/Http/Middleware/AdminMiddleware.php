<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
            return redirect()->route('login.admin')->with('error', 'Vui lòng đăng nhập để truy cập.');
        }

        // Check if user has admin role
        if (Auth::user()->role !== 'admin') {
            // Redirect based on user role
            if (Auth::user()->role === 'teacher') {
                return redirect()->route('teacher.dashboard')->with('error', 'Bạn không có quyền truy cập khu vực Admin.');
            } elseif (Auth::user()->role === 'student') {
                return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập khu vực Admin.');
            }
            
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
