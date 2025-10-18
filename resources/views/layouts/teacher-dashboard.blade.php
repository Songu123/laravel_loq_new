@extends('layouts.app')

@section('title', 'Teacher Dashboard')
@section('body-class', 'teacher-dashboard-page')

@push('styles')
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
<style>
/* Ẩn navbar cho teacher dashboard */
body.teacher-dashboard-page nav.navbar {
    display: none !important;
}

/* Adjust main content to take full height */
body.teacher-dashboard-page .main-content {
    min-height: 100vh;
    padding: 0;
}

/* Hide footer for teacher dashboard */
body.teacher-dashboard-page footer {
    display: none !important;
}

/* Teacher-specific colors */
.teacher-dashboard-wrapper .sidebar {
    background: linear-gradient(180deg, #059669 0%, #047857 100%);
}

.teacher-dashboard-wrapper .sidebar-brand {
    color: #ffffff;
}

.teacher-dashboard-wrapper .sidebar-brand i {
    color: #10b981;
}

.teacher-dashboard-wrapper .nav-link:hover,
.teacher-dashboard-wrapper .nav-link.active {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}

.teacher-dashboard-wrapper .topbar {
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
}

/* User avatar in topbar */
.user-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #059669;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

/* Topbar dropdown styling */
.topbar .dropdown-menu {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border-radius: 12px;
    padding: 0.5rem;
    min-width: 200px;
}

.topbar .dropdown-item {
    border-radius: 8px;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.topbar .dropdown-item:hover {
    background: #f3f4f6;
}

.topbar .dropdown-header {
    font-size: 0.875rem;
    color: #6b7280;
    padding: 0.5rem 1rem;
}

/* Sidebar user actions */
.user-info {
    display: flex;
    align-items: center;
    position: relative;
}

.user-details {
    flex: 1;
    margin-left: 0.75rem;
    margin-right: 0.5rem;
}

.user-actions .dropdown-menu {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border-radius: 12px;
    padding: 0.5rem;
    min-width: 180px;
}

.user-actions .dropdown-item {
    border-radius: 8px;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
    color: #374151;
}

.user-actions .dropdown-item:hover {
    background: #f3f4f6;
}
</style>
@endpush

@section('content')
<div class="teacher-dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <i class="bi bi-mortarboard-fill"></i>
                <span class="sidebar-brand-text">LOQ Teacher</span>
            </div>
            <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                <i class="bi bi-x"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-header">Tổng quan</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('teacher.dashboard') ?? '#' }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-house-door"></i>
                            <span>Trang chủ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-graph-up"></i>
                            <span>Thống kê lớp học</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-header">Quản lý đề thi</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="/teacher/exams/create" class="nav-link">
                            <i class="bi bi-file-earmark-plus"></i>
                            <span>Tạo đề thi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/teacher/exams" class="nav-link">
                            <i class="bi bi-list-ul"></i>
                            <span>Đề thi của tôi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-question-circle"></i>
                            <span>Ngân hàng câu hỏi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.categories.index') }}" class="nav-link {{ request()->routeIs('teacher.categories.*') ? 'active' : '' }}">
                            <i class="bi bi-tags"></i>
                            <span>Danh mục môn học</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-header">Lớp học</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-people"></i>
                            <span>Học sinh của tôi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-clipboard-data"></i>
                            <span>Kết quả thi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-calendar-event"></i>
                            <span>Lịch thi</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-header">Báo cáo</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-bar-chart"></i>
                            <span>Báo cáo lớp học</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-download"></i>
                            <span>Xuất báo cáo</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-header">Cài đặt</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-person-gear"></i>
                            <span>Hồ sơ cá nhân</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-gear"></i>
                            <span>Cài đặt</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- User Info in Sidebar -->
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'T', 0, 1)) }}
                </div>
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->name ?? 'Teacher' }}</div>
                    <div class="user-role">{{ Auth::user()->getRoleDisplayName() ?? 'Giáo viên' }}</div>
                </div>
                <div class="user-actions">
                    <div class="dropdown dropup">
                        <button class="btn btn-sm btn-outline-light" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Cài đặt</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay d-lg-none" id="sidebarOverlay"></div>
    
    <!-- Main Content -->
    <main class="main-dashboard">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle d-lg-none" id="sidebarToggleTop">
                    <i class="bi bi-list"></i>
                </button>
                <div class="breadcrumb-container">
                    @yield('breadcrumb')
                </div>
            </div>
            <div class="topbar-right">
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <button class="btn btn-outline-success btn-sm me-2" data-bs-toggle="tooltip" title="Tạo đề thi nhanh">
                        <i class="bi bi-plus"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm me-3" data-bs-toggle="tooltip" title="Tìm kiếm">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                
                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar-small me-2">
                            {{ strtoupper(substr(Auth::user()->name ?? 'T', 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Teacher' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ Auth::user()->email ?? 'teacher@example.com' }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Cài đặt</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-question-circle me-2"></i>Trợ giúp</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <div class="dashboard-content">
            @yield('teacher-dashboard-content')
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggle = document.getElementById('sidebarToggleTop');
    const sidebarClose = document.getElementById('sidebarToggle');
    
    function toggleSidebar() {
        sidebar.classList.toggle('show');
        sidebarOverlay.classList.toggle('show');
    }
    
    function closeSidebar() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-collapse sidebar on mobile when clicking nav links
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });
});
</script>
@endpush