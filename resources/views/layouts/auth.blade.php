@extends('layouts.app')

@section('title', 'Xác thực')
@section('body-class', 'auth-page')

@push('styles')
<link href="{{ asset('css/auth.css') }}" rel="stylesheet">
<style>
/* Auth-specific overrides */
.main-content { padding: 0; min-height: 100vh; }
footer { display: none; }
body.auth-page { 
    background: #0d6efd;
    overflow-x: hidden;
}

/* Auth Header */
.auth-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    padding: 1rem 2rem;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1050;
}

.auth-header .btn-light {
    background: transparent;
    border: 1px solid #dee2e6;
    transition: all 0.3s;
}

.auth-header .btn-light:hover {
    background: #f8f9fa;
    border-color: #0d6efd;
}

.auth-header .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
}

.auth-header .user-dropdown {
    cursor: pointer;
    transition: all 0.3s;
}

.auth-header .user-dropdown:hover {
    opacity: 0.8;
}

.auth-container {
    padding-top: 80px;
}

/* Notifications badge */
.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 18px;
    height: 18px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<!-- Auth Header -->
<div class="auth-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Left: Logo & Title -->
            <div class="d-flex align-items-center">
                <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center">
                    <i class="bi bi-mortarboard-fill text-primary fs-3 me-2"></i>
                    <span class="fw-bold text-dark fs-5">LOQ</span>
                </a>
                <span class="ms-3 text-muted d-none d-md-inline">Hệ thống Trắc nghiệm</span>
            </div>
            
            <!-- Right: Actions -->
            <div class="d-flex align-items-center gap-3">
                @auth
                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="btn btn-light position-relative" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 300px;">
                            <li><h6 class="dropdown-header">Thông báo mới</h6></li>
                            <li><a class="dropdown-item py-2" href="#">
                                <div class="d-flex">
                                    <i class="bi bi-info-circle text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">5 phút trước</small>
                                        <span class="small">Đề thi mới: Laravel Basics</span>
                                    </div>
                                </div>
                            </a></li>
                            <li><a class="dropdown-item py-2" href="#">
                                <div class="d-flex">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">1 giờ trước</small>
                                        <span class="small">Bài thi của bạn đã được chấm điểm</span>
                                    </div>
                                </div>
                            </a></li>
                            <li><a class="dropdown-item py-2" href="#">
                                <div class="d-flex">
                                    <i class="bi bi-trophy text-warning me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">2 giờ trước</small>
                                        <span class="small">Chúc mừng! Bạn đạt điểm cao nhất lớp</span>
                                    </div>
                                </div>
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center small text-primary" href="#">Xem tất cả thông báo</a></li>
                        </ul>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="dropdown">
                        <div class="d-flex align-items-center user-dropdown" 
                             data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar me-2">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="d-none d-md-block">
                                <div class="fw-semibold small">{{ auth()->user()->name }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    @if(auth()->user()->isStudent())
                                        Sinh viên
                                    @elseif(auth()->user()->isTeacher())
                                        Giáo viên
                                    @elseif(auth()->user()->isAdmin())
                                        Quản trị viên
                                    @endif
                                </small>
                            </div>
                            <i class="bi bi-chevron-down ms-2"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="{{ route(auth()->user()->role . '.dashboard') }}">
                                <i class="bi bi-house-door me-2"></i>Dashboard
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i>Hồ sơ
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i>Cài đặt
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                @else
                    <!-- Guest Actions -->
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-person-plus me-1"></i> Đăng ký
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

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

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Toast Container for Notifications -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 80px;">
    @if(session('success'))
    <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-x-circle-fill me-2"></i>
                {{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
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
    
    // Auto-hide toasts
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toast => {
        const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
        bsToast.show();
    });
    
    // Animate header on scroll
    let lastScroll = 0;
    const header = document.querySelector('.auth-header');
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 50) {
            header.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';
        } else {
            header.style.boxShadow = '0 0.15rem 1.75rem rgba(58, 59, 69, 0.15)';
        }
        
        lastScroll = currentScroll;
    });
});
</script>
@endpush