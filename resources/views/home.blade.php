@extends('layouts.app')

@section('title', 'Trang chủ - LOQ Quiz')

@push('styles')
<style>
    /* Hero Banner with Image */
    .hero-banner {
        position: relative;
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.9) 0%, rgba(108, 117, 125, 0.9) 100%),
                    url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=1920&h=600&fit=crop') center/cover;
        color: white;
        padding: 5rem 0;
        margin-bottom: 3rem;
        overflow: hidden;
    }
    
    .hero-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }
    
    .hero-content {
        position: relative;
        z-index: 1;
    }
    
    .hero-title {
        font-size: 3rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        margin-bottom: 1.5rem;
        animation: fadeInUp 1s ease-out;
    }
    
    .hero-subtitle {
        font-size: 1.25rem;
        opacity: 0.95;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        margin-bottom: 2rem;
        animation: fadeInUp 1s ease-out 0.2s both;
    }
    
    .hero-features {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        margin-top: 2rem;
        animation: fadeInUp 1s ease-out 0.4s both;
    }
    
    .hero-feature-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: rgba(255, 255, 255, 0.1);
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .hero-feature-item i {
        font-size: 1.5rem;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .hero-image-wrapper {
        position: relative;
        animation: fadeInRight 1s ease-out 0.3s both;
    }
    
    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .floating-badge {
        position: absolute;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .floating-badge-1 {
        top: 20%;
        right: -10%;
        animation-delay: 0s;
    }
    
    .floating-badge-2 {
        bottom: 20%;
        right: 0;
        animation-delay: 1s;
    }
    
    .hero-section {
        background: #0d6efd;
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
    }
    
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .exam-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
        height: 100%;
    }
    
    .exam-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }
    
    .exam-header {
        background: #f8f9fa;
        padding: 1.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    .exam-badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.5rem;
    }
    
    .badge-easy {
        background: #d1fae5;
        color: #065f46;
    }
    
    .badge-medium {
        background: #fef3c7;
        color: #92400e;
    }
    
    .badge-hard {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }
    
    .cta-button {
        background: #0d6efd;
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .cta-button:hover {
        background: #0a58ca;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(13, 110, 253, 0.3);
        color: white;
    }
    
    .login-required-overlay {
        position: relative;
    }
    
    .login-required-overlay::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 16px;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<!-- Hero Banner with Image -->
<div class="hero-banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">
                        <i class="bi bi-mortarboard-fill me-3"></i>
                        Hệ Thống Thi Trắc Nghiệm
                    </h1>
                    <p class="hero-subtitle">
                        Nền tảng thi trực tuyến hiện đại, giúp bạn học tập hiệu quả và đạt kết quả cao nhất. 
                        Hơn 10,000+ học viên tin tưởng sử dụng!
                    </p>
                    
                    @guest
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Đăng nhập ngay
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">
                                <i class="bi bi-person-plus me-2"></i>
                                Đăng ký miễn phí
                            </a>
                        </div>
                    @else
                        <div class="d-flex gap-3 flex-wrap">
                            @if(Auth::user()->isStudent())
                                <a href="{{ route('student.dashboard') }}" class="btn btn-light btn-lg px-4">
                                    <i class="bi bi-speedometer2 me-2"></i>
                                    Dashboard của tôi
                                </a>
                            @elseif(Auth::user()->isTeacher())
                                <a href="{{ route('teacher.dashboard') }}" class="btn btn-light btn-lg px-4">
                                    <i class="bi bi-speedometer2 me-2"></i>
                                    Dashboard Giáo viên
                                </a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-lg px-4">
                                    <i class="bi bi-speedometer2 me-2"></i>
                                    Dashboard Admin
                                </a>
                            @endif
                        </div>
                    @endguest
                    
                    <div class="hero-features">
                        <div class="hero-feature-item">
                            <i class="bi bi-shield-check"></i>
                            <span>Bảo mật cao</span>
                        </div>
                        <div class="hero-feature-item">
                            <i class="bi bi-lightning-charge"></i>
                            <span>Thi nhanh chóng</span>
                        </div>
                        <div class="hero-feature-item">
                            <i class="bi bi-graph-up"></i>
                            <span>Theo dõi tiến độ</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=600&h=500&fit=crop" 
                         alt="Online Learning" 
                         class="img-fluid rounded-4 shadow-lg"
                         style="border: 5px solid rgba(255,255,255,0.2);">
                    
                    <!-- Floating Badges -->
                    <div class="floating-badge floating-badge-1">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-3"></i>
                            <div>
                                <div class="fw-bold text-dark">1,200+</div>
                                <small class="text-muted">Đề thi</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="floating-badge floating-badge-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-trophy-fill text-warning fs-3"></i>
                            <div>
                                <div class="fw-bold text-dark">98%</div>
                                <small class="text-muted">Hài lòng</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Old Hero Section - Now simplified or can be removed -->
<div class="hero-section d-none">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="bi bi-mortarboard-fill me-3"></i>
                    Hệ thống Thi Trắc Nghiệm Quiz
                </h1>
                <p class="lead mb-4">Nền tảng thi trực tuyến hiện đại, dễ sử dụng và hiệu quả cho giáo dục</p>
                
                @guest
                    <!-- Login/Register Options -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-white bg-opacity-10 text-white h-100">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-person-circle display-4"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">Dành cho Sinh viên</h5>
                                    <p class="small opacity-90 mb-4">Tham gia các bài thi và xem kết quả</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('login.student') }}" class="btn btn-light">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>
                                            Đăng nhập
                                        </a>
                                        <a href="{{ route('register.student') }}" class="btn btn-outline-light">
                                            <i class="bi bi-person-plus me-2"></i>
                                            Đăng ký
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 bg-white bg-opacity-10 text-white h-100">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-person-workspace display-4"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">Dành cho Giáo viên</h5>
                                    <p class="small opacity-90 mb-4">Quản lý đề thi và theo dõi kết quả</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('login.teacher') }}" class="btn btn-light">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>
                                            Đăng nhập
                                        </a>
                                        <a href="{{ route('register.teacher') }}" class="btn btn-outline-light">
                                            <i class="bi bi-person-plus me-2"></i>
                                            Đăng ký
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="d-flex gap-3 flex-wrap">
                        @if(Auth::user()->isStudent())
                            <a href="{{ route('student.dashboard') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard của tôi
                            </a>
                        @elseif(Auth::user()->isTeacher())
                            <a href="{{ route('teacher.dashboard') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard Giáo viên
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard Admin
                            </a>
                        @endif
                    </div>
                @endguest
            </div>
            
            <div class="col-lg-4 d-none d-lg-block text-center">
                <i class="bi bi-file-earmark-text display-1"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="container mb-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon text-primary">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <h3 class="fw-bold mb-1">{{ $stats['total_exams'] }}</h3>
                <p class="text-muted mb-0">Đề thi</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon text-success">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <h3 class="fw-bold mb-1">{{ $stats['total_categories'] }}</h3>
                <p class="text-muted mb-0">Danh mục</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon text-info">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($stats['total_attempts']) }}</h3>
                <p class="text-muted mb-0">Lượt thi</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="container">
    <div class="filter-section">
        <form method="GET" action="{{ route('home') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-search me-1"></i>Tìm kiếm
                </label>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Tìm tên đề thi...">
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-folder me-1"></i>Danh mục
                </label>
                <select class="form-select" name="category">
                    <option value="">Tất cả</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold">
                    <i class="bi bi-bar-chart me-1"></i>Độ khó
                </label>
                <select class="form-select" name="difficulty">
                    <option value="">Tất cả</option>
                    <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Dễ</option>
                    <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                    <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Khó</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold">
                    <i class="bi bi-sort-down me-1"></i>Sắp xếp
                </label>
                <select class="form-select" name="sort">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến</option>
                    <option value="easiest" {{ request('sort') == 'easiest' ? 'selected' : '' }}>Dễ nhất</option>
                    <option value="hardest" {{ request('sort') == 'hardest' ? 'selected' : '' }}>Khó nhất</option>
                </select>
            </div>
            
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Exams List -->
<div class="container mb-5">
    <h2 class="mb-4 fw-bold">
        <i class="bi bi-grid-fill me-2"></i>
        Đề thi có sẵn
    </h2>
    
    @if($exams->count() > 0)
        <div class="row g-4">
            @foreach($exams as $exam)
                <div class="col-md-6 col-lg-4">
                    <div class="card exam-card {{ auth()->guest() ? 'login-required-overlay' : '' }}">
                        <div class="exam-header">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="exam-badge badge-{{ $exam->difficulty_level == 'easy' ? 'easy' : ($exam->difficulty_level == 'medium' ? 'medium' : 'hard') }}">
                                    @if($exam->difficulty_level == 'easy')
                                        <i class="bi bi-star me-1"></i>Dễ
                                    @elseif($exam->difficulty_level == 'medium')
                                        <i class="bi bi-star-fill me-1"></i>Trung bình
                                    @else
                                        <i class="bi bi-stars me-1"></i>Khó
                                    @endif
                                </span>
                                <span class="badge bg-primary">
                                    <i class="bi bi-clock me-1"></i>{{ $exam->duration_minutes }} phút
                                </span>
                            </div>
                            <h5 class="fw-bold mb-1">{{ $exam->title }}</h5>
                            <small class="text-muted">
                                <i class="bi bi-folder me-1"></i>{{ $exam->category->name }}
                            </small>
                        </div>
                        
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                {{ Str::limit($exam->description, 100) }}
                            </p>
                            
                            <div class="d-flex justify-content-between text-muted small mb-3">
                                <span>
                                    <i class="bi bi-question-circle me-1"></i>
                                    {{ $exam->total_questions }} câu
                                </span>
                                <span>
                                    <i class="bi bi-star me-1"></i>
                                    {{ $exam->total_marks }} điểm
                                </span>
                            </div>
                            
                            @auth
                                @if(Auth::user()->isStudent())
                                    <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-primary w-100">
                                        <i class="bi bi-play-circle me-2"></i>Vào thi
                                    </a>
                                @else
                                    <button class="btn btn-outline-secondary w-100" disabled>
                                        Chỉ dành cho sinh viên
                                    </button>
                                @endif
                            @else
                                <button class="btn btn-primary w-100" onclick="showLoginPrompt()">
                                    <i class="bi bi-lock me-2"></i>Đăng nhập để thi
                                </button>
                            @endguest
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-5 d-flex justify-content-center">
            {{ $exams->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h4 class="text-muted mt-3">Chưa có đề thi nào</h4>
            <p class="text-muted">Vui lòng quay lại sau</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function showLoginPrompt() {
    if (confirm('Bạn cần đăng nhập để tham gia thi. Đăng nhập ngay?')) {
        window.location.href = '{{ route('login.student') }}';
    }
}
</script>
@endpush
