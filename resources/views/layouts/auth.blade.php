@extends('layouts.app')

@section('title', 'Xác thực')
@section('body-class', 'auth-page')

@push('styles')
<link href="{{ asset('css/auth.css') }}" rel="stylesheet">
<style>
/* Auth-specific overrides */
.navbar { display: none; }
.main-content { padding: 0; min-height: 100vh; }
footer { display: none; }
body.auth-page { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    overflow-x: hidden;
}
</style>
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-background">
        <div class="auth-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
    
    <div class="container-fluid h-100">
        <div class="row h-100 g-0">
            <!-- Left Panel - Branding -->
            <div class="col-lg-7 d-none d-lg-flex auth-branding-panel">
                <div class="auth-branding">
                    <div class="auth-logo">
                        <i class="bi bi-mortarboard-fill"></i>
                        <span>LOQ</span>
                    </div>
                    <h1 class="auth-title">@yield('auth-title', 'Hệ thống Trắc nghiệm')</h1>
                    <p class="auth-subtitle">@yield('auth-subtitle', 'Nền tảng quản lý thi trắc nghiệm hiện đại cho giáo dục')</p>
                    
                    <div class="auth-features">
                        <div class="feature-item">
                            <i class="bi bi-shield-check"></i>
                            <span>Bảo mật cao</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-graph-up"></i>
                            <span>Thống kê chi tiết</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-people"></i>
                            <span>Quản lý người dùng</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-laptop"></i>
                            <span>Đa nền tảng</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Panel - Auth Form -->
            <div class="col-lg-5 d-flex align-items-center justify-content-center">
                <div class="auth-form-container">
                    <div class="auth-form-header text-center mb-4">
                        <div class="auth-logo-mobile d-lg-none mb-3">
                            <i class="bi bi-mortarboard-fill"></i>
                            <span>LOQ</span>
                        </div>
                        <h2 class="auth-form-title">@yield('form-title')</h2>
                        <p class="auth-form-subtitle text-muted">@yield('form-subtitle')</p>
                    </div>
                    
                    @yield('auth-content')
                    
                    <div class="auth-form-footer text-center mt-4">
                        @yield('auth-footer')
                        
                        <div class="auth-help mt-3">
                            <small class="text-muted">
                                Cần hỗ trợ? 
                                <a href="mailto:support@loq.edu.vn" class="text-decoration-none">Liên hệ chúng tôi</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate auth elements on load
    const authElements = document.querySelectorAll('.auth-form-container > *');
    authElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        setTimeout(() => {
            el.style.transition = 'all 0.6s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Form validation feedback
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
                submitBtn.disabled = true;
            }
        });
    });
});
</script>
@endpush