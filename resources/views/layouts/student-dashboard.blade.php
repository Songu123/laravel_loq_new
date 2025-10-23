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
        
        /* Top Navbar */
        .top-navbar {
            background: #0d6efd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 70px;
        }
        
        .top-navbar .brand {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .top-navbar .brand:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .top-navbar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            border-radius: 0.35rem;
            text-decoration: none;
            font-weight: 500;
        }
        
        .top-navbar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .top-navbar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .top-navbar .nav-link i {
            font-size: 1.1rem;
        }
        
        /* User Dropdown */
        .user-dropdown {
            cursor: pointer;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.35rem;
            transition: background 0.3s;
        }
        
        .user-dropdown:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a58ca;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Notification Badge */
        .notification-btn {
            background: transparent;
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 0.35rem;
            position: relative;
            transition: background 0.3s;
        }
        
        .notification-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .notification-btn i {
            font-size: 1.25rem;
        }
        
        /* Main Content */
        .main-content {
            margin-top: 70px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: #6c757d;
        }
        
        .breadcrumb-item a {
            color: #0d6efd;
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: #0a58ca;
            text-decoration: underline;
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            transition: box-shadow 0.3s;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #0d6efd;
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: #0d6efd;
        }
        
        /* Stats Cards */
        .stat-card {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }
        
        .stat-icon.primary {
            background-color: #e7f1ff;
            color: #0d6efd;
        }
        
        .stat-icon.success {
            background-color: #d1f4e5;
            color: #198754;
        }
        
        .stat-icon.warning {
            background-color: #fff3cd;
            color: #ffc107;
        }
        
        .stat-icon.info {
            background-color: #cfe2ff;
            color: #0dcaf0;
        }
        
        /* Buttons */
        .btn {
            border-radius: 0.35rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-primary:hover {
            background-color: #0a58ca;
            border-color: #0a58ca;
        }
        
        /* Badge */
        .badge {
            padding: 0.35rem 0.65rem;
            font-weight: 500;
            border-radius: 0.25rem;
        }
        
        /* Dropdown Menu */
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: background 0.3s;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fc;
            color: #0d6efd;
        }
        
        .dropdown-item i {
            width: 20px;
            text-align: center;
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            padding: 0.25rem 0.5rem;
        }
        
        .mobile-nav {
            display: none;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .mobile-menu-toggle {
                display: block;
            }
            
            .desktop-nav {
                display: none;
            }
            
            .mobile-nav {
                display: block;
                position: fixed;
                top: 70px;
                left: -100%;
                width: 280px;
                height: calc(100vh - 70px);
                background: white;
                box-shadow: 2px 0 4px rgba(0,0,0,0.1);
                transition: left 0.3s;
                overflow-y: auto;
                z-index: 999;
            }
            
            .mobile-nav.show {
                left: 0;
            }
            
            .mobile-nav .nav-link {
                color: #495057;
                padding: 1rem 1.5rem;
                border-left: 3px solid transparent;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                text-decoration: none;
                transition: all 0.3s;
            }
            
            .mobile-nav .nav-link:hover {
                background-color: #f8f9fc;
                border-left-color: #0d6efd;
                color: #0d6efd;
            }
            
            .mobile-nav .nav-link.active {
                background-color: #e7f1ff;
                border-left-color: #0d6efd;
                color: #0d6efd;
                font-weight: 600;
            }
            
            .main-content {
                padding: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .top-navbar {
                height: 60px;
            }
            
            .main-content {
                margin-top: 60px;
                padding: 0.75rem;
            }
            
            .mobile-nav {
                top: 60px;
                height: calc(100vh - 60px);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center h-100">
                <!-- Brand & Menu -->
                <div class="d-flex align-items-center gap-3">
                    <button class="mobile-menu-toggle" onclick="toggleMobileNav()">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <a href="{{ route('student.dashboard') }}" class="brand">
                        <i class="bi bi-mortarboard-fill"></i>
                        <span>Student Portal</span>
                    </a>
                    
                    <!-- Desktop Navigation -->
                    <div class="desktop-nav d-none d-lg-flex gap-2 ms-4">
                        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" 
                           href="{{ route('student.dashboard') }}">
                            <i class="bi bi-house-door"></i>
                            <span>Dashboard</span>
                        </a>
                        
                        <a class="nav-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}" 
                           href="{{ route('student.exams.index') }}">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Đề thi</span>
                        </a>
                        
                        <a class="nav-link {{ request()->routeIs('student.practice.*') ? 'active' : '' }}"
                           href="{{ route('student.practice.index') }}">
                            <i class="bi bi-trophy"></i>
                            <span>Luyện tập</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('student.classes.*') ? 'active' : '' }}"
                           href="{{ route('student.classes.index') }}">
                            <i class="bi bi-people"></i>
                            <span>Lớp học</span>
                        </a>
                        
                        <a class="nav-link {{ request()->routeIs('student.history') ? 'active' : '' }}"
                           href="{{ route('student.history') }}">
                            <i class="bi bi-clock-history"></i>
                            <span>Lịch sử & Kết quả</span>
                        </a>
                    </div>
                </div>
                
                <!-- Right Side -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Notifications -->
                    <div class="dropdown">
                        <button id="notifDropdownBtn" class="notification-btn" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" 
                                  style="font-size: 0.65rem;">0</span>
                        </button>
                        <ul id="notifDropdown" class="dropdown-menu dropdown-menu-end" style="min-width: 340px;">
                            <li>
                                <div class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span>Thông báo</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span id="notifHeaderCount" class="badge bg-primary rounded-pill">0</span>
                                        <button id="markAllReadBtn" class="btn btn-link btn-sm p-0 text-decoration-none">Đánh dấu đã đọc</button>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <div id="notifListContainer">
                                <li class="px-3 py-2 text-muted small" id="notifEmpty">Không có thông báo</li>
                                <!-- Items will be injected here -->
                            </div>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center small text-primary fw-semibold" href="{{ route('student.notifications') }}">
                                    Xem tất cả thông báo
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="dropdown">
                        <div class="d-flex align-items-center user-dropdown" 
                             data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar me-2">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="d-none d-md-block me-2">
                                <div class="fw-semibold" style="font-size: 0.9rem;">{{ auth()->user()->name }}</div>
                                <small style="font-size: 0.75rem; opacity: 0.85;">Sinh viên</small>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-header">
                                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-person"></i>Hồ sơ cá nhân
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-gear"></i>Cài đặt
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-question-circle"></i>Trợ giúp
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <div class="mobile-nav" id="mobileNav">
        <div class="p-3 border-bottom">
            <div class="fw-bold text-primary">Menu chính</div>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" 
               href="{{ route('student.dashboard') }}">
                <i class="bi bi-house-door"></i>
                <span>Dashboard</span>
            </a>
            
            <a class="nav-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}" 
               href="{{ route('student.exams.index') }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Đề thi</span>
            </a>
            
            <a class="nav-link {{ request()->routeIs('student.practice.*') ? 'active' : '' }}"
               href="{{ route('student.practice.index') }}">
                <i class="bi bi-trophy"></i>
                <span>Luyện tập</span>
            </a>
            <a class="nav-link {{ request()->routeIs('student.classes.*') ? 'active' : '' }}"
               href="{{ route('student.classes.index') }}">
                <i class="bi bi-people"></i>
                <span>Lớp học</span>
            </a>
            
            <a class="nav-link {{ request()->routeIs('student.history') ? 'active' : '' }}"
               href="{{ route('student.history') }}">
                <i class="bi bi-clock-history"></i>
                <span>Lịch sử & Kết quả</span>
            </a>
            
            <hr class="my-2 mx-3">
            
            <a class="nav-link" href="#">
                <i class="bi bi-person"></i>
                <span>Hồ sơ cá nhân</span>
            </a>
            
            <a class="nav-link" href="#">
                <i class="bi bi-gear"></i>
                <span>Cài đặt</span>
            </a>
            
            <a class="nav-link text-danger" href="#" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Đăng xuất</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('breadcrumb')
        
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
        // Mobile nav toggle
        function toggleMobileNav() {
            const mobileNav = document.getElementById('mobileNav');
            mobileNav.classList.toggle('show');
        }
        
        // Close mobile nav when clicking outside
        document.addEventListener('click', function(e) {
            const mobileNav = document.getElementById('mobileNav');
            const toggleBtn = document.querySelector('.mobile-menu-toggle');
            
            if (mobileNav && mobileNav.classList.contains('show')) {
                if (!mobileNav.contains(e.target) && !toggleBtn.contains(e.target)) {
                    mobileNav.classList.remove('show');
                }
            }
        });
        
        // Close mobile nav when link is clicked
        document.querySelectorAll('.mobile-nav .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobileNav').classList.remove('show');
            });
        });
        
        // Auto-hide toasts
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => {
                const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
                bsToast.show();
            });

            // Notifications boot
            initNotifications();
        });

        // ---------------- Notifications ----------------
        function initNotifications() {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const unreadUrl = `{{ url('/user/notifications/unread') }}`;
            const listUrl = `{{ url('/user/notifications') }}`;
            const readAllUrl = `{{ url('/user/notifications/read-all') }}`;
            const readBaseUrl = `{{ url('/user/notifications') }}`;
            const resultsBaseUrl = `{{ url('/student/results') }}`;
            const examsBaseUrl = `{{ url('/student/exams') }}`;
            const badge = document.getElementById('notifBadge');
            const headerCount = document.getElementById('notifHeaderCount');
            const listContainer = document.getElementById('notifListContainer');
            const emptyItem = document.getElementById('notifEmpty');
            const dropdownBtn = document.getElementById('notifDropdownBtn');
            const markAllBtn = document.getElementById('markAllReadBtn');

            async function fetchUnreadCount() {
                try {
                    const res = await fetch(unreadUrl, {
                        credentials: 'same-origin'
                    });
                    if (!res.ok) return;
                    const json = await res.json();
                    const items = json.data?.data || json.data || [];
                    const count = items.length || (json.unread_count ?? 0);
                    updateBadge(count);
                } catch (e) { /* ignore */ }
            }

            async function fetchLatest() {
                try {
                    const res = await fetch(listUrl, {
                        credentials: 'same-origin'
                    });
                    if (!res.ok) return;
                    const json = await res.json();
                    const items = json.data?.data || json.data || [];
                    renderList(items.slice(0, 10));
                    const unread = json.unread_count ?? 0;
                    updateBadge(unread);
                } catch (e) { renderList([]); }
            }

            function updateBadge(count) {
                headerCount.textContent = count;
                if (count > 0) {
                    badge.textContent = count > 9 ? '9+' : count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            }

            function iconFor(item) {
                const type = item.data?.type || item.type || '';
                if (type === 'exam_completed') return '<i class="bi bi-check-circle text-success"></i>';
                if (type === 'exam_published') return '<i class="bi bi-file-earmark-plus text-primary"></i>';
                if (type === 'exam_reminder') return '<i class="bi bi-alarm text-warning"></i>';
                return '<i class="bi bi-bell text-secondary"></i>';
            }

            function targetUrl(item) {
                const d = item.data || {};
                if (d.type === 'exam_completed' && d.attempt_id) {
                    return resultsBaseUrl + '/' + d.attempt_id;
                }
                if ((d.type === 'exam_published' || d.type === 'exam_reminder') && d.exam_id) {
                    return examsBaseUrl + '/' + d.exam_id;
                }
                return '#';
            }

            function timeAgo(dateStr) {
                try {
                    const d = new Date(dateStr);
                    const diff = Math.floor((Date.now() - d.getTime())/1000);
                    if (diff < 60) return diff + ' giây trước';
                    if (diff < 3600) return Math.floor(diff/60) + ' phút trước';
                    if (diff < 86400) return Math.floor(diff/3600) + ' giờ trước';
                    return Math.floor(diff/86400) + ' ngày trước';
                } catch { return ''; }
            }

            function renderList(items) {
                listContainer.innerHTML = '';
                if (!items || items.length === 0) {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-2 text-muted small';
                    li.textContent = 'Không có thông báo';
                    listContainer.appendChild(li);
                    return;
                }
                items.forEach(item => {
                    const li = document.createElement('li');
                    const url = targetUrl(item);
                    li.innerHTML = `
                        <a class="dropdown-item py-2 ${item.read_at ? '' : 'fw-semibold'}" href="#" data-id="${item.id}" data-url="${url}">
                            <div class="d-flex">
                                <div class="flex-shrink-0">${iconFor(item)}</div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small">${(item.data?.message) ?? 'Thông báo'}</div>
                                    <div class="text-muted small">${timeAgo(item.created_at)}</div>
                                </div>
                            </div>
                        </a>`;
                    listContainer.appendChild(li);
                });

                // bind click
                listContainer.querySelectorAll('a.dropdown-item').forEach(a => {
                    a.addEventListener('click', async (e) => {
                        e.preventDefault();
                        const id = a.getAttribute('data-id');
                        const url = a.getAttribute('data-url');
                        try {
                            await fetch(`${readBaseUrl}/${id}/read`, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': csrf },
                                credentials: 'same-origin'
                            });
                        } catch {}
                        window.location.href = url;
                    });
                });
            }

            // mark all as read
            if (markAllBtn) {
                markAllBtn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    try {
                        await fetch(readAllUrl, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf },
                            credentials: 'same-origin'
                        });
                        updateBadge(0);
                        fetchLatest();
                    } catch {}
                });
            }

            // Fetch when dropdown is opened
            dropdownBtn?.addEventListener('click', () => {
                fetchLatest();
            });

            // Poll unread count
            fetchUnreadCount();
            setInterval(fetchUnreadCount, 30000);
        }
    </script>
    
    @stack('scripts')
</body>
</html>
