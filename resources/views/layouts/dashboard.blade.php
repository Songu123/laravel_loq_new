@extends('layouts.app')

@section('title', 'Dashboard')
@section('body-class', 'dashboard-page')

@push('styles')
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
<style>
/* Ẩn navbar cho dashboard */
body.dashboard-page nav.navbar {
    display: none !important;
}

/* Adjust main content to take full height */
body.dashboard-page .main-content {
    min-height: 100vh;
    padding: 0;
}

/* Hide footer for dashboard */
body.dashboard-page footer {
    display: none !important;
}

/* User avatar in topbar */
.user-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #4f46e5;
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
<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <i class="bi bi-mortarboard-fill"></i>
                <span class="sidebar-brand-text">LOQ Admin</span>
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
                        <a href="{{ route('dashboard') ?? '#' }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-house-door"></i>
                            <span>Trang chủ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-graph-up"></i>
                            <span>Thống kê</span>
                            <span class="badge bg-primary ms-auto">Mới</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-header">Quản lý đề thi</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-file-earmark-plus"></i>
                            <span>Tạo đề thi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-list-ul"></i>
                            <span>Danh sách đề thi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-question-circle"></i>
                            <span>Ngân hàng câu hỏi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-tags"></i>
                            <span>Danh mục</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-header">Học viên</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-people"></i>
                            <span>Quản lý học viên</span>
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
                            <i class="bi bi-award"></i>
                            <span>Xếp hạng</span>
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
                            <span>Báo cáo tổng quan</span>
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
                <div class="nav-section-header">Hệ thống</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-gear"></i>
                            <span>Cài đặt</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-shield-check"></i>
                            <span>Phân quyền</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- User Info in Sidebar -->
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->name ?? 'User' }}</div>
                    <div class="user-role">Quản trị viên</div>
                </div>
                <div class="user-actions">
                    <div class="dropdown dropup">
                        <button class="btn btn-sm btn-outline-light" 
                                id="sidebarUserDropdown"
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sidebarUserDropdown">
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
                    <button class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="tooltip" title="Tạo đề thi nhanh">
                        <i class="bi bi-plus"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm me-3" data-bs-toggle="tooltip" title="Tìm kiếm">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                
                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" 
                            type="button" 
                            id="userDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <div class="user-avatar-small me-2">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'User' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><h6 class="dropdown-header">{{ Auth::user()->email ?? 'user@example.com' }}</h6></li>
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
            @yield('dashboard-content')
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Đảm bảo Bootstrap đã load
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JS not loaded!');
        return;
    }
    
    console.log('Bootstrap loaded successfully');
    
    // Initialize dropdown manually nếu cần
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    dropdownElementList.forEach(function(dropdownToggleEl) {
        new bootstrap.Dropdown(dropdownToggleEl);
    });
    
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
    
    // Debug dropdown
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            console.log('Dropdown clicked:', this);
        });
    });
});
</script>
@endpush