<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login.student');
        }

        if (!auth()->user()->isStudent()) {
            abort(403, 'Chỉ học sinh mới có thể truy cập trang này.');
        }

        if (!auth()->user()->is_active) {
            abort(403, 'Tài khoản của bạn đã bị vô hiệu hóa.');
        }

        return $next($request);
    }
}