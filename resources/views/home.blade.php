@extends('layouts.app')

@section('title', 'Trang chủ - LOQ Quiz')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
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
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 1.5rem;
        border-bottom: 2px solid #e2e8f0;
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
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .cta-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
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
<!-- Hero Section -->
<div class="hero-section">
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
