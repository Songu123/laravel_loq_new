@extends('layouts.teacher-dashboard')

@section('title', 'Kết quả thi - ' . $exam->title)

@section('teacher-dashboard-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.index') }}">Lớp học</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.show', $class) }}">{{ $class->name }}</a></li>
                    <li class="breadcrumb-item active">{{ $exam->title }}</li>
                </ol>
            </nav>
            <h4 class="mt-2 mb-0">Kết quả thi: {{ $exam->title }}</h4>
        </div>
        <a href="{{ route('teacher.classes.show', $class) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-2">Tổng số học sinh</h5>
                    <h2 class="text-primary mb-0">{{ $totalStudents }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-2">Đã làm bài</h5>
                    <h2 class="text-info mb-0">{{ $studentsAttempted }}</h2>
                    <small class="text-muted">{{ $totalStudents > 0 ? round($studentsAttempted/$totalStudents*100, 1) : 0 }}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-2">Đạt yêu cầu (≥50%)</h5>
                    <h2 class="text-success mb-0">{{ $passedCount }}</h2>
                    <small class="text-muted">{{ $studentsAttempted > 0 ? round($passedCount/$studentsAttempted*100, 1) : 0 }}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-2">Điểm trung bình</h5>
                    <h2 class="text-warning mb-0">{{ $averageScore ? round($averageScore, 1) : 'N/A' }}%</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-table"></i> Bảng kết quả chi tiết</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Tên học sinh</th>
                        <th>Email</th>
                        <th class="text-center">Số lần thi</th>
                        <th class="text-center">Điểm cao nhất</th>
                        <th class="text-center">Điểm trung bình</th>
                        <th class="text-center">Lần thi gần nhất</th>
                        <th class="text-center">Vi phạm</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        @php
                            $attempts = $student->examAttempts;
                            $attemptCount = $attempts->count();
                            $highestScore = $attemptCount > 0 ? $attempts->max('percentage') : null;
                            $avgScore = $attemptCount > 0 ? $attempts->avg('percentage') : null;
                            $latestAttempt = $attempts->first();
                            $totalViolations = $attempts->sum(function($a) { return $a->violations->count(); });
                        @endphp
                        <tr class="{{ $attemptCount == 0 ? 'table-secondary' : '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $student->name }}</strong>
                                @if($attemptCount == 0)
                                    <span class="badge bg-secondary ms-2">Chưa thi</span>
                                @endif
                            </td>
                            <td>{{ $student->email }}</td>
                            <td class="text-center">
                                @if($attemptCount > 0)
                                    <span class="badge bg-primary">{{ $attemptCount }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($highestScore !== null)
                                    <span class="badge {{ $highestScore >= 80 ? 'bg-success' : ($highestScore >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ round($highestScore, 1) }}%
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($avgScore !== null)
                                    {{ round($avgScore, 1) }}%
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($latestAttempt)
                                    <small class="text-muted">{{ $latestAttempt->created_at->format('d/m/Y H:i') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($totalViolations > 0)
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-triangle"></i> {{ $totalViolations }}
                                    </span>
                                @else
                                    @if($attemptCount > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> 0
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-end">
                                @if($attemptCount > 0)
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#attempts-{{ $student->id }}">
                                        <i class="bi bi-eye"></i> Chi tiết
                                    </button>
                                @else
                                    <span class="text-muted small">Chưa có dữ liệu</span>
                                @endif
                            </td>
                        </tr>
                        @if($attemptCount > 0)
                            <tr class="collapse" id="attempts-{{ $student->id }}">
                                <td colspan="9" class="bg-light">
                                    <div class="p-3">
                                        <h6 class="mb-3">Lịch sử làm bài của {{ $student->name }}:</h6>
                                        <table class="table table-sm table-bordered bg-white mb-0">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 50px;">Lần</th>
                                                    <th>Thời gian làm bài</th>
                                                    <th>Điểm</th>
                                                    <th>Đúng/Tổng</th>
                                                    <th>Thời lượng</th>
                                                    <th>Vi phạm</th>
                                                    <th class="text-end">Xem</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($attempts as $attemptIndex => $attempt)
                                                    <tr>
                                                        <td class="text-center">{{ $attemptIndex + 1 }}</td>
                                                        <td>{{ $attempt->created_at->format('d/m/Y H:i:s') }}</td>
                                                        <td>
                                                            <span class="badge {{ $attempt->percentage >= 80 ? 'bg-success' : ($attempt->percentage >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                                                {{ round($attempt->percentage, 1) }}%
                                                            </span>
                                                        </td>
                                                        <td>{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</td>
                                                        <td>
                                                            @if($attempt->time_taken)
                                                                {{ gmdate('H:i:s', $attempt->time_taken) }}
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($attempt->violations->count() > 0)
                                                                <span class="badge bg-danger">
                                                                    {{ $attempt->violations->count() }} vi phạm
                                                                </span>
                                                            @else
                                                                <span class="badge bg-success">Không có</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            @if($attempt->violations->count() > 0)
                                                                <a href="{{ route('teacher.violations.report', $attempt) }}" 
                                                                   class="btn btn-sm btn-outline-danger"
                                                                   target="_blank">
                                                                    <i class="bi bi-flag"></i> Vi phạm
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Chưa có học sinh nào trong lớp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.breadcrumb-item + .breadcrumb-item::before {
    color: #6c757d;
}
</style>
@endsection
