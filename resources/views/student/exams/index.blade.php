@extends('layouts.student-dashboard')

@section('title', 'Đề thi')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Đề thi</li>
    </ol>
</nav>
@endsection

@section('student-dashboard-content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tất cả đề thi</h1>
            <p class="text-muted mb-0">Khám phá và làm các bài kiểm tra</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('student.exams.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" placeholder="Tìm kiếm đề thi...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="category">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="difficulty">
                            <option value="">Tất cả độ khó</option>
                            <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Dễ</option>
                            <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Khó</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="sort">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến</option>
                            <option value="easiest" {{ request('sort') == 'easiest' ? 'selected' : '' }}>Dễ nhất</option>
                            <option value="hardest" {{ request('sort') == 'hardest' ? 'selected' : '' }}>Khó nhất</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Exams Grid -->
    @if($exams->count() > 0)
        <div class="row">
            @foreach($exams as $exam)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card exam-card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">{{ $exam->title }}</h5>
                                    <div class="text-muted small">
                                        <i class="bi bi-folder" style="color: {{ $exam->category->color ?? '#4e73df' }}"></i>
                                        {{ $exam->category->name }}
                                    </div>
                                </div>
                                @if($exam->is_new)
                                    <span class="badge bg-danger">Mới</span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <p class="text-muted small mb-3">{{ Str::limit($exam->description, 120) }}</p>

                            <!-- Stats -->
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="stat-box">
                                        <i class="bi bi-question-circle text-primary"></i>
                                        <div class="fw-bold">{{ $exam->questions_count }}</div>
                                        <small class="text-muted">Câu hỏi</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box">
                                        <i class="bi bi-star text-warning"></i>
                                        <div class="fw-bold">{{ $exam->total_marks }}</div>
                                        <small class="text-muted">Điểm</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box">
                                        <i class="bi bi-clock text-info"></i>
                                        <div class="fw-bold">{{ $exam->duration_minutes }}</div>
                                        <small class="text-muted">Phút</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Badges -->
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-{{ $exam->difficulty_level === 'easy' ? 'success' : ($exam->difficulty_level === 'medium' ? 'warning' : 'danger') }}">
                                    <i class="bi bi-speedometer"></i> {{ $exam->difficulty_level_text }}
                                </span>
                                @if($exam->attempts_count > 0)
                                    <span class="badge bg-info">
                                        <i class="bi bi-people"></i> {{ $exam->attempts_count }} lượt thi
                                    </span>
                                @endif
                            </div>

                            <!-- My Status -->
                            @if(isset($exam->my_attempt))
                                <div class="alert alert-success mb-3 py-2">
                                    <small>
                                        <i class="bi bi-check-circle me-1"></i>
                                        Đã thi - Điểm: <strong>{{ $exam->my_attempt->score }}/{{ $exam->total_marks }}</strong>
                                    </small>
                                </div>
                            @endif

                            <!-- Time Info -->
                            @if($exam->start_time || $exam->end_time)
                                <div class="small text-muted mb-3">
                                    @if($exam->start_time && $exam->start_time->isFuture())
                                        <i class="bi bi-calendar-event text-warning"></i> 
                                        Bắt đầu: {{ $exam->start_time->format('d/m/Y H:i') }}
                                    @elseif($exam->end_time && $exam->end_time->isPast())
                                        <i class="bi bi-lock text-danger"></i> 
                                        Đã kết thúc
                                    @elseif($exam->end_time)
                                        <i class="bi bi-hourglass-split text-info"></i> 
                                        Kết thúc: {{ $exam->end_time->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-white border-top">
                            <div class="d-flex gap-2">
                                <a href="{{ route('student.exams.show', $exam) }}" 
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="bi bi-eye"></i> Chi tiết
                                </a>
                                @if($exam->canTake)
                                    <a href="{{ route('student.exams.take', $exam) }}" 
                                       class="btn btn-primary btn-sm flex-fill">
                                        <i class="bi bi-play-circle"></i> 
                                        {{ isset($exam->my_attempt) ? 'Thi lại' : 'Bắt đầu' }}
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-sm flex-fill" disabled>
                                        <i class="bi bi-lock"></i> Không khả dụng
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($exams->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $exams->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h5 class="text-muted mt-3">Không tìm thấy đề thi</h5>
            <p class="text-muted">Thử thay đổi bộ lọc để xem các đề thi khác.</p>
            <a href="{{ route('student.exams.index') }}" class="btn btn-primary mt-2">
                <i class="bi bi-arrow-clockwise"></i> Xem tất cả
            </a>
        </div>
    @endif
</div>

<style>
.exam-card {
    transition: all 0.3s ease;
}

.exam-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
}

.stat-box {
    padding: 0.5rem;
}

.stat-box i {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}

.stat-box .fw-bold {
    font-size: 1.25rem;
    line-height: 1;
}

.stat-box small {
    display: block;
    font-size: 0.75rem;
}

.card-header {
    padding: 1rem 1.25rem;
}

.card-footer {
    padding: 0.75rem 1.25rem;
}
</style>
@endsection
