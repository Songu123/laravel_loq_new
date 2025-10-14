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
