@extends('layouts.auth')

@section('auth-title', 'Chào mừng trở lại')
@section('auth-subtitle', 'Đăng nhập để quản lý đề thi, người dùng và kết quả trắc nghiệm của bạn. Hệ thống dành cho quản trị viên và giảng viên.')

@section('form-title', 'Đăng nhập')
@section('form-subtitle', 'Nhập thông tin đăng nhập để tiếp tục')

@section('auth-content')
<form method="POST" action="{{ route('login') }}" class="auth-form">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" 
               type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               name="email" 
               value="{{ old('email') }}" 
               required 
               autofocus
               placeholder="Nhập địa chỉ email của bạn">
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Mật khẩu</label>
        <div class="position-relative">
            <input id="password" 
                   type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   name="password" 
                   required
                   placeholder="Nhập mật khẩu của bạn">
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
    
    <!-- Divider -->
    <div class="position-relative my-4">
        <hr class="text-muted">
        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">
            Hoặc đăng nhập với
        </span>
    </div>
    
    <!-- Social Login Buttons -->
    <div class="d-grid gap-2 mb-3">
        <a href="{{ route('auth.google') }}" class="btn btn-outline-danger">
            <svg class="me-2" width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                <path fill="none" d="M0 0h48v48H0z"/>
            </svg>
            Đăng nhập với Google
        </a>
        
        <!-- Facebook Login (optional - for future) -->
        <!--
        <a href="{{ route('auth.facebook') }}" class="btn btn-outline-primary">
            <i class="bi bi-facebook me-2"></i>
            Đăng nhập với Facebook
        </a>
        -->
    </div>
</form>
@endsection

@section('auth-footer')
<p class="mb-0">
    Chưa có tài khoản? 
    <a href="{{ route('register') }}" class="fw-semibold">Đăng ký ngay</a>
</p>
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
