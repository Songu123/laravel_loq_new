@extends('layouts.auth')

@section('auth-title', 'Tham gia LOQ')
@section('auth-subtitle', 'Tạo tài khoản quản trị hoặc giảng viên để quản lý đề thi, câu hỏi và thống kê kết quả. Hệ thống hỗ trợ xuất báo cáo và phân quyền cơ bản.')

@section('form-title', 'Tạo tài khoản')
@section('form-subtitle', 'Điền thông tin để tạo tài khoản mới')

@section('auth-content')
<form method="POST" action="{{ route('register') }}" class="auth-form">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Họ và tên</label>
        <input id="name" 
               type="text" 
               class="form-control @error('name') is-invalid @enderror" 
               name="name" 
               value="{{ old('name') }}" 
               required 
               autofocus
               placeholder="Nhập họ và tên đầy đủ">
        @error('name')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" 
               type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               name="email" 
               value="{{ old('email') }}" 
               required
               placeholder="Nhập địa chỉ email">
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
                   placeholder="Tạo mật khẩu mạnh">
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
        <div class="mt-1">
            <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự</small>
        </div>
    </div>

    <div class="mb-4">
        <label for="password-confirm" class="form-label">Xác nhận mật khẩu</label>
        <div class="position-relative">
            <input id="password-confirm" 
                   type="password" 
                   class="form-control" 
                   name="password_confirmation" 
                   required
                   placeholder="Nhập lại mật khẩu">
            <button type="button" 
                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3" 
                    id="togglePasswordConfirm"
                    style="border: none; background: none; color: #6b7280;">
                <i class="bi bi-eye" id="toggleIconConfirm"></i>
            </button>
        </div>
    </div>

    <div class="mb-4">
        <div class="form-check">
            <input class="form-check-input" 
                   type="checkbox" 
                   id="terms" 
                   required>
            <label class="form-check-label" for="terms">
                Tôi đồng ý với 
                <a href="#" class="text-decoration-none">Điều khoản sử dụng</a> 
                và 
                <a href="#" class="text-decoration-none">Chính sách bảo mật</a>
            </label>
        </div>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary" id="registerBtn">
            <i class="bi bi-person-plus me-2"></i>
            Tạo tài khoản
        </button>
    </div>
</form>
@endsection

@section('auth-footer')
<p class="mb-0">
    Đã có tài khoản? 
    <a href="{{ route('login') }}" class="fw-semibold">Đăng nhập</a>
</p>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    function setupPasswordToggle(toggleId, passwordId, iconId) {
        const toggle = document.getElementById(toggleId);
        const password = document.getElementById(passwordId);
        const icon = document.getElementById(iconId);
        
        if (toggle && password && icon) {
            toggle.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                if (type === 'password') {
                    icon.className = 'bi bi-eye';
                } else {
                    icon.className = 'bi bi-eye-slash';
                }
            });
        }
    }
    
    setupPasswordToggle('togglePassword', 'password', 'toggleIcon');
    setupPasswordToggle('togglePasswordConfirm', 'password-confirm', 'toggleIconConfirm');
    
    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password-confirm');
    
    function validatePasswordMatch() {
        if (passwordConfirm.value && password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Mật khẩu xác nhận không khớp');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }
    
    if (password && passwordConfirm) {
        password.addEventListener('input', validatePasswordMatch);
        passwordConfirm.addEventListener('input', validatePasswordMatch);
    }
    
    // Form submission handling
    const registerForm = document.querySelector('.auth-form');
    const registerBtn = document.getElementById('registerBtn');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function() {
            registerBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tạo tài khoản...';
            registerBtn.disabled = true;
        });
    }
    
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            // You can add visual feedback here
        });
    }
    
    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        return strength;
    }
});
</script>
@endpush
