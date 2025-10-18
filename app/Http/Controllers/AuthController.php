<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // show register form
    public function showRegister()
    {
        return view('auth.register');
    }

    // show admin register form
    public function showRegisterAdmin()
    {
        return view('auth.register_admin');
    }

    // show teacher register form
    public function showRegisterTeacher()
    {
        return view('auth.teacher-register');
    }

    // show student register form
    public function showRegisterStudent()
    {
        return view('auth.student-register');
    }

    // handle general register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','max:255', Rule::unique('users','email')],
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,teacher,student',
            'student_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'student_id' => $data['student_id'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        Auth::login($user);

        // Redirect based on role
        return $this->redirectBasedOnRole($user);
    }

    // handle admin register
    public function registerAdmin(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','max:255', Rule::unique('users','email')],
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Đăng ký Admin thành công!');
    }

    // handle teacher register
    public function registerTeacher(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','max:255', Rule::unique('users','email')],
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'teacher',
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        Auth::login($user);
        return redirect()->route('teacher.dashboard')->with('success', 'Đăng ký Giáo viên thành công!');
    }

    // handle student register
    public function registerStudent(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','max:255', Rule::unique('users','email')],
            'password' => 'required|string|min:6|confirmed',
            'student_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'student',
            'student_id' => $data['student_id'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        Auth::login($user);
        return redirect()->route('student.dashboard')->with('success', 'Đăng ký Học sinh thành công!');
    }

    // show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // show admin login form
    public function showLoginAdmin()
    {
        return view('auth.login_admin');
    }

    // show teacher login form
    public function showLoginTeacher()
    {
        return view('auth.teacher-login');
    }

    // show student login form
    public function showLoginStudent()
    {
        return view('auth.student-login');
    }

    // handle general login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    // handle admin login
    public function loginAdmin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is admin or teacher
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->route('dashboard')->with('success', 'Đăng nhập thành công!');
            } elseif ($user->role === 'teacher') {
                $request->session()->regenerate();
                return redirect()->route('teacher.dashboard')->with('success', 'Đăng nhập thành công!');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Bạn không có quyền truy cập vào khu vực này.',
                ])->onlyInput('email');
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    // handle teacher login
    public function loginTeacher(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is teacher
            if ($user->role === 'teacher') {
                $request->session()->regenerate();
                return redirect()->route('teacher.dashboard')->with('success', 'Đăng nhập thành công!');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tài khoản này không phải là tài khoản giáo viên.',
                ])->onlyInput('email');
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    // handle student login
    public function loginStudent(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is student
            if ($user->role === 'student') {
                $request->session()->regenerate();
                return redirect()->route('student.dashboard')->with('success', 'Đăng nhập thành công!');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tài khoản này không phải là tài khoản sinh viên.',
                ])->onlyInput('email');
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    // logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Bạn đã đăng xuất.');
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole($user)
    {
        return match($user->role) {
            'admin' => redirect()->route('dashboard')->with('success', 'Đăng nhập thành công!'),
            'teacher' => redirect()->route('teacher.dashboard')->with('success', 'Đăng nhập thành công!'),
            'student' => redirect()->route('home')->with('success', 'Đăng nhập thành công!'),
            default => redirect()->route('home')->with('success', 'Đăng nhập thành công!')
        };
    }
}
