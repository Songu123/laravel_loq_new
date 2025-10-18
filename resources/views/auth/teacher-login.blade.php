@extends('layouts.auth')

@section('auth-title', 'Đăng nhập - Giáo viên')
@section('auth-subtitle', 'Đăng nhập để quản lý đề thi, câu hỏi và theo dõi kết quả học sinh')

@section('form-title', 'Đăng nhập Giáo viên')
@section('form-subtitle', 'Nhập thông tin để tiếp tục')

@section('auth-content')
<form method="POST" action="{{ route('login.teacher.post') }}" class="auth-form">
    @csrf
    <input type="hidden" name="role" value="teacher">

    <div class="mb-3">
        <label for="email" class="form-label">
            <i class="bi bi-envelope me-2"></i>Email
        </label>
        <input id="email" 
               type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               name="email" 
               value="{{ old('email') }}" 
               required 
               autofocus
               placeholder="Nhập email giáo viên">
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">
            <i class="bi bi-lock me-2"></i>Mật khẩu
        </label>
        <div class="position-relative">
            <input id="password" 
                   type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   name="password" 
                   required
                   placeholder="Nhập mật khẩu">
            <button type="button" 
                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3" 
                    id="togglePassword"
                    style="border: none; background: none; color: #6b7280;">
                <i class="bi bi-eye" id="toggleIcon"></i>
            </button>
        </div>
        @error('password')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" 
                   type="checkbox" 
                   name="remember" 
                   id="remember" 
                   {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                Ghi nhớ đăng nhập
            </label>
        </div>

        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-decoration-none">
                Quên mật khẩu?
            </a>
        @endif
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary" id="loginBtn">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Đăng nhập
        </button>
    </div>

    <div class="text-center">
        <small class="text-muted">
            <i class="bi bi-person-workspace me-1"></i>
            Đăng nhập với tư cách Giáo viên
        </small>
    </div>
</form>
@endsection

@section('auth-footer')
<div class="text-center">
    <p class="mb-2">
        Chưa có tài khoản giáo viên? 
        <a href="{{ route('register.teacher') }}" class="fw-semibold">Đăng ký ngay</a>
    </p>
    <p class="mb-0">
        <a href="{{ route('login.student') }}" class="text-decoration-none">
            <i class="bi bi-person-circle me-1"></i>Đăng nhập với tư cách Sinh viên
        </a>
    </p>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            if (type === 'password') {
                toggleIcon.className = 'bi bi-eye';
            } else {
                toggleIcon.className = 'bi bi-eye-slash';
            }
        });
    }
    
    // Form submission handling
    const loginForm = document.querySelector('.auth-form');
    const loginBtn = document.getElementById('loginBtn');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang đăng nhập...';
            loginBtn.disabled = true;
        });
    }
});
</script>
@endpush
