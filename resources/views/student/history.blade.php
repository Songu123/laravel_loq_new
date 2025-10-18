@extends('layouts.student-dashboard')

@section('title', 'Lịch sử thi')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Lịch sử thi</li>
    </ol>
</nav>
@endsection

@section('student-dashboard-content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Lịch sử thi</h1>
            <p class="text-muted mb-0">Xem lại các bài thi đã hoàn thành</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Tổng số bài thi</div>
                            <h4 class="mb-0">{{ $totalAttempts ?? 0 }}</h4>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-file-earmark-check" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Đạt yêu cầu</div>
                            <h4 class="mb-0">{{ $passedAttempts ?? 0 }}</h4>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Điểm trung bình</div>
                            <h4 class="mb-0">{{ number_format($averageScore ?? 0, 1) }}%</h4>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-bar-chart" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Điểm cao nhất</div>
                            <h4 class="mb-0">{{ number_format($highestScore ?? 0, 1) }}%</h4>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-trophy" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('student.history') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" 
                               value="{{ request('search') }}" placeholder="Tìm kiếm đề thi...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="category">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="result">
                            <option value="">Tất cả kết quả</option>
                            <option value="passed" {{ request('result') == 'passed' ? 'selected' : '' }}>Đạt (≥50%)</option>
                            <option value="failed" {{ request('result') == 'failed' ? 'selected' : '' }}>Chưa đạt (<50%)</option>
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

    <!-- Results List -->
    @if($attempts->count() > 0)
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Đề thi</th>
                                <th style="width: 150px;">Thời gian</th>
                                <th style="width: 120px;">Điểm</th>
                                <th style="width: 120px;">Kết quả</th>
                                <th style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                                <tr>
                                    <td>{{ $loop->iteration + ($attempts->currentPage() - 1) * $attempts->perPage() }}</td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $attempt->exam->title }}</div>
                                            <div class="text-muted small">
                                                <span class="badge" style="background-color: {{ $attempt->exam->category->color }}20; color: {{ $attempt->exam->category->color }};">
                                                    {{ $attempt->exam->category->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            {{ $attempt->created_at->format('d/m/Y') }}<br>
                                            {{ $attempt->created_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $attempt->score }}/{{ $attempt->exam->total_marks }}</div>
                                        <small class="text-muted">{{ $attempt->correct_answers }}/{{ $attempt->total_questions }} đúng</small>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="badge bg-{{ $attempt->percentage >= 80 ? 'success' : ($attempt->percentage >= 50 ? 'warning' : 'danger') }} mb-1">
                                                {{ number_format($attempt->percentage, 1) }}%
                                            </div>
                                            <div class="text-muted small">
                                                {{ $attempt->percentage >= 50 ? '✓ Đạt' : '✗ Chưa đạt' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('student.results.show', $attempt) }}" 
                                               class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('student.exams.show', $attempt->exam) }}" 
                                               class="btn btn-outline-secondary" title="Thi lại">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($attempts->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $attempts->links() }}
            </div>
        @endif
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Chưa có lịch sử thi</h5>
                    <p class="text-muted">Bắt đầu làm bài thi đầu tiên của bạn!</p>
                    <a href="{{ route('student.exams.index') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-play-circle"></i> Xem đề thi
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
