<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Dashboard') - {{ config('app.name', 'Exam System') }}</title>
    
    <!-- Bootstrap 5.3.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Global Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: #f8f9fc;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(99, 102, 241, 0.25);
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: #fff;
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.15);
            border-left-color: #fff;
            font-weight: 600;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        /* Topbar */
        .topbar {
            background: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 1rem 1.5rem;
            margin: -2rem -2rem 2rem -2rem;
            border-radius: 0.35rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            color: #4e73df;
        }
        
        /* Stats Cards */
        .stat-card {
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        /* Buttons */
        .btn {
            border-radius: 0.35rem;
            font-weight: 500;
        }
        
        /* Badge */
        .badge {
            padding: 0.35rem 0.65rem;
            font-weight: 500;
        }
        
        /* User Dropdown */
        .user-dropdown {
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .sidebar.show {
                width: 250px;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="p-4 text-center border-bottom border-white border-opacity-25">
            <h4 class="text-white mb-0">
                <i class="bi bi-mortarboard-fill"></i> Student Portal
            </h4>
            <p class="text-white-50 small mb-0">Hệ thống thi trắc nghiệm</p>
        </div>
        
        <nav class="nav flex-column p-3">
            <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" 
               href="{{ route('student.dashboard') }}">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
            
            <a class="nav-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}" 
               href="{{ route('student.exams.index') }}">
                <i class="bi bi-file-earmark-text"></i> Đề thi
            </a>
            
            <a class="nav-link {{ request()->routeIs('student.history') ? 'active' : '' }}"
               href="{{ route('student.history') }}">
                <i class="bi bi-clock-history"></i> Lịch sử thi & Kết quả
            </a>            <hr class="border-white border-opacity-25 my-3">
            
            <a class="nav-link" href="#">
                <i class="bi bi-person"></i> Hồ sơ
            </a>
            
            <a class="nav-link" href="#">
                <i class="bi bi-gear"></i> Cài đặt
            </a>
            
            <a class="nav-link text-danger" href="#" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @yield('breadcrumb')
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="btn btn-light position-relative" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Thông báo</h6></li>
                            <li><a class="dropdown-item" href="#">
                                <small class="text-muted">5 phút trước</small><br>
                                Đề thi mới: Laravel Basics
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center small" href="#">Xem tất cả</a></li>
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
                                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                <small class="text-muted">Sinh viên</small>
                            </div>
                            <i class="bi bi-chevron-down ms-2"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
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
                </div>
            </div>
        </div>

        <!-- Page Content -->
        @yield('student-dashboard-content')
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Toast Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" id="successToast">
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
        <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" id="errorToast">
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
    
    <!-- Confetti Animation -->
    @if(session('show_confetti'))
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        // Confetti animation for high scores
        setTimeout(() => {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
            
            // Multiple bursts
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 }
                });
            }, 200);
            
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 }
                });
            }, 400);
        }, 500);
    </script>
    @endif
    
    <!-- Custom Scripts -->
    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
        
        // Auto-hide toasts
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => {
                const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
                bsToast.show();
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
