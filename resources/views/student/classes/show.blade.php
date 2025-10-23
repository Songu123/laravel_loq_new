@extends('layouts.student-dashboard')

@section('title', $class->name)

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb px-2">
    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student.classes.index') }}">Lớp học</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $class->name }}</li>
  </ol>
</nav>
@endsection

@section('student-dashboard-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $class->name }}</h4>
            <div class="text-muted small">Giáo viên: {{ optional($class->teacher)->name }}</div>
        </div>
        <div>
            <a href="{{ route('student.classes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách lớp
            </a>
        </div>
    </div>

    @if($class->description)
        <div class="card mb-3">
            <div class="card-body">
                <div class="text-muted">{{ $class->description }}</div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Đề thi trong lớp</span>
            <span class="badge bg-primary">{{ $class->exams->count() }}</span>
        </div>
        <div class="card-body p-0">
            @if($class->exams->isEmpty())
                <div class="p-3 text-muted">Chưa có đề thi nào trong lớp này.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Đề thi</th>
                                <th class="d-none d-md-table-cell">Chủ đề</th>
                                <th class="d-none d-md-table-cell">Thời lượng</th>
                                <th class="d-none d-lg-table-cell">Thời gian</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($class->exams as $exam)
                            @php 
                                $now = now();
                                $status = 'Sắp mở';
                                $badge = 'secondary';
                                if ($exam->isAvailable()) {
                                    $status = 'Đang mở';
                                    $badge = 'success';
                                } elseif ($exam->end_time && $now->gt($exam->end_time)) {
                                    $status = 'Đã kết thúc';
                                    $badge = 'secondary';
                                } elseif ($exam->start_time && $now->lt($exam->start_time)) {
                                    $status = 'Chưa mở';
                                    $badge = 'warning';
                                } elseif (!$exam->is_active) {
                                    $status = 'Tạm khóa';
                                    $badge = 'secondary';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $exam->title }}</div>
                                    <div class="text-muted small d-md-none">
                                        {{ optional($exam->category)->name }} · {{ $exam->duration_text }}
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">{{ optional($exam->category)->name }}</td>
                                <td class="d-none d-md-table-cell">{{ $exam->duration_text }}</td>
                                <td class="d-none d-lg-table-cell">
                                    @if($exam->start_time)
                                        <div class="small">Bắt đầu: {{ $exam->start_time->format('d/m/Y H:i') }}</div>
                                    @endif
                                    @if($exam->end_time)
                                        <div class="small">Kết thúc: {{ $exam->end_time->format('d/m/Y H:i') }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $badge }}">{{ $status }}</span>
                                </td>
                                <td class="text-end">
                                    @if($exam->isAvailable())
                                        <a href="{{ route('student.exams.take', $exam) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-play-circle"></i> Làm bài
                                        </a>
                                    @else
                                        <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i> Xem chi tiết
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
