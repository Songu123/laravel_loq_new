<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <i class="bi bi-mortarboard-fill me-2"></i>
            LOQ
            <span class="d-none d-sm-inline text-light opacity-90 ms-1">Quiz</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left Navigation -->
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item">
                        @if(Auth::user()->isAdmin())
                            <a class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}" 
                               href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-house-door me-1"></i>
                                Dashboard
                            </a>
                        @elseif(Auth::user()->isTeacher())
                            <a class="nav-link {{ request()->routeIs('teacher.dashboard*') ? 'active' : '' }}" 
                               href="{{ route('teacher.dashboard') }}">
                                <i class="bi bi-house-door me-1"></i>
                                Dashboard
                            </a>
                        @else
                            <a class="nav-link {{ request()->routeIs('home*') ? 'active' : '' }}" 
                               href="{{ route('home') }}">
                                <i class="bi bi-house-door me-1"></i>
                                Trang chủ
                            </a>
                        @endif
                    </li>
                    @if(Auth::user()->isAdmin() || Auth::user()->isTeacher())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-file-earmark-text me-1"></i>
                                Quản lý đề thi
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-plus-circle me-2"></i>Tạo đề thi</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-list-ul me-2"></i>Danh sách đề thi</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-question-circle me-2"></i>Ngân hàng câu hỏi</a></li>
                                @if(Auth::user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags me-2"></i>Danh mục</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-graph-up me-1"></i>
                                Báo cáo
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-bar-chart me-2"></i>Thống kê</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-people me-2"></i>Kết quả học viên</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Xuất báo cáo</a></li>
                            </ul>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Right Navigation -->
            <ul class="navbar-nav">
                @auth
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                3
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Thông báo</h6></li>
                            <li><a class="dropdown-item" href="#"><small class="text-muted">5 phút trước</small><br>Có 3 bài thi mới cần chấm</a></li>
                            <li><a class="dropdown-item" href="#"><small class="text-muted">1 giờ trước</small><br>Học viên Nguyễn Văn A đã hoàn thành bài thi</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">Xem tất cả</a></li>
                        </ul>
                    </li>

                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2" 
                                 style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'User' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
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
                    </li>
                @else
                    <!-- Guest Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Đăng nhập
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-1"></i>
                            Đăng ký
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>