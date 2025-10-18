@extends('layouts.student-dashboard')

@section('title', $exam->title)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.exams.index') }}">Đề thi</a></li>
        <li class="breadcrumb-item active">{{ $exam->title }}</li>
    </ol>
</nav>
@endsection

@section('student-dashboard-content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h3 class="mb-0">{{ $exam->title }}</h3>
                </div>

                <div class="card-body">
                    <!-- Category & Meta -->
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge" style="background-color: {{ $exam->category->color }}20; color: {{ $exam->category->color }}; font-size: 1rem; padding: 0.5rem 1rem;">
                            <i class="bi bi-folder"></i> {{ $exam->category->name }}
                        </span>
                        <span class="ms-3 text-muted">
                            <i class="bi bi-calendar"></i> 
                            {{ $exam->created_at->format('d/m/Y') }}
                        </span>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="bi bi-info-circle text-primary"></i> Mô tả</h5>
                        <p class="text-muted">{{ $exam->description ?: 'Không có mô tả' }}</p>
                    </div>

                    <hr>

                    <!-- Stats Grid -->
                    <div class="row text-center mb-4">
                        <div class="col-3">
                            <div class="stat-item-detail">
                                <div class="stat-icon text-primary mb-2">
                                    <i class="bi bi-question-circle" style="font-size: 2.5rem;"></i>
                                </div>
                                <h3 class="mb-0">{{ $exam->questions_count }}</h3>
                                <small class="text-muted">Câu hỏi</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item-detail">
                                <div class="stat-icon text-success mb-2">
                                    <i class="bi bi-star" style="font-size: 2.5rem;"></i>
                                </div>
                                <h3 class="mb-0">{{ $exam->total_marks }}</h3>
                                <small class="text-muted">Tổng điểm</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item-detail">
                                <div class="stat-icon text-info mb-2">
                                    <i class="bi bi-clock" style="font-size: 2.5rem;"></i>
                                </div>
                                <h3 class="mb-0">{{ $exam->duration_minutes }}</h3>
                                <small class="text-muted">Phút</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item-detail">
                                <div class="stat-icon text-warning mb-2">
                                    <i class="bi bi-speedometer" style="font-size: 2.5rem;"></i>
                                </div>
                                <h5 class="mb-0 badge bg-{{ $exam->difficulty_level === 'easy' ? 'success' : ($exam->difficulty_level === 'medium' ? 'warning' : 'danger') }}">
                                    {{ $exam->difficulty_level_text }}
                                </h5>
                                <small class="text-muted">Độ khó</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Question Types Breakdown -->
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="bi bi-list-check text-success"></i> Phân loại câu hỏi</h5>
                        <div class="row">
                            @php
                                $questionTypes = [
                                    'multiple_choice' => ['name' => 'Trắc nghiệm', 'icon' => 'check2-square', 'color' => 'primary'],
                                    'true_false' => ['name' => 'Đúng/Sai', 'icon' => 'toggle-on', 'color' => 'success'],
                                    'short_answer' => ['name' => 'Trả lời ngắn', 'icon' => 'chat-left-text', 'color' => 'info'],
                                    'essay' => ['name' => 'Tự luận', 'icon' => 'file-text', 'color' => 'warning']
                                ];
                                $questionStats = $exam->questions->groupBy('question_type')->map->count();
                            @endphp
                            @foreach($questionTypes as $type => $info)
                                @if($questionStats->has($type))
                                    <div class="col-6 col-md-3 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-{{ $info['icon'] }} text-{{ $info['color'] }} me-2" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <div class="fw-bold">{{ $questionStats[$type] }}</div>
                                                <small class="text-muted">{{ $info['name'] }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Hướng dẫn làm bài</h6>
                        <ul class="mb-0">
                            <li>Đọc kỹ đề bài trước khi trả lời</li>
                            <li>Thời gian làm bài là <strong>{{ $exam->duration_minutes }} phút</strong></li>
                            <li>Bạn có thể xem lại và sửa đáp án trước khi nộp bài</li>
                            @if($exam->randomize_questions)
                                <li>Câu hỏi sẽ được xáo trộn ngẫu nhiên</li>
                            @endif
                            @if($exam->show_results)
                                <li>Kết quả sẽ hiển thị ngay sau khi nộp bài</li>
                            @endif
                        </ul>
                    </div>

                    <!-- My Previous Attempts -->
                    @if(isset($myAttempts) && $myAttempts->count() > 0)
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="bi bi-clock-history text-warning"></i> Lịch sử thi của bạn</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Lần</th>
                                            <th>Thời gian</th>
                                            <th>Điểm</th>
                                            <th>Kết quả</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($myAttempts as $attempt)
                                            <tr>
                                                <td>#{{ $loop->iteration }}</td>
                                                <td>{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                                                <td><strong>{{ $attempt->score }}/{{ $exam->total_marks }}</strong></td>
                                                <td>
                                                    <span class="badge bg-{{ $attempt->percentage >= 80 ? 'success' : ($attempt->percentage >= 50 ? 'warning' : 'danger') }}">
                                                        {{ number_format($attempt->percentage, 1) }}%
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('student.results.show', $attempt) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Xem
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Start Exam Card -->
            <div class="card shadow-sm mb-4 sticky-top" style="top: 1rem;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-play-circle"></i> Bắt đầu làm bài</h5>
                </div>
                <div class="card-body">
                    @if($canTake)
                        <div class="text-center mb-3">
                            <div class="mb-3">
                                <i class="bi bi-clock-history text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="text-primary">{{ $exam->duration_minutes }} phút</h4>
                            <p class="text-muted mb-0">Thời gian làm bài</p>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <a href="{{ route('student.exams.take', $exam) }}" 
                               class="btn btn-primary btn-lg">
                                <i class="bi bi-play-circle-fill"></i> 
                                {{ isset($myAttempts) && $myAttempts->count() > 0 ? 'Thi lại' : 'Bắt đầu thi' }}
                            </a>
                            <a href="{{ route('student.exams.index') }}" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>

                        @if(isset($myAttempts) && $myAttempts->count() > 0)
                            <div class="alert alert-warning mt-3 mb-0">
                                <small>
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Bạn đã thi {{ $myAttempts->count() }} lần. Điểm cao nhất: 
                                    <strong>{{ $myAttempts->max('score') }}/{{ $exam->total_marks }}</strong>
                                </small>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-lock" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Không thể thi</h5>
                            @if($exam->start_time && $exam->start_time->isFuture())
                                <p>Đề thi chưa mở.<br>Bắt đầu: {{ $exam->start_time->format('d/m/Y H:i') }}</p>
                            @elseif($exam->end_time && $exam->end_time->isPast())
                                <p>Đề thi đã kết thúc.</p>
                            @elseif(!$exam->is_active)
                                <p>Đề thi đang tạm dừng.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Additional Info -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin thêm</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Người tạo</small>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                {{ substr($exam->creator->name, 0, 1) }}
                            </div>
                            <strong>{{ $exam->creator->name }}</strong>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Ngày tạo</small>
                        <strong>{{ $exam->created_at->format('d/m/Y H:i') }}</strong>
                    </div>

                    @if($exam->start_time)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Thời gian bắt đầu</small>
                            <strong>{{ $exam->start_time->format('d/m/Y H:i') }}</strong>
                        </div>
                    @endif

                    @if($exam->end_time)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Thời gian kết thúc</small>
                            <strong>{{ $exam->end_time->format('d/m/Y H:i') }}</strong>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $exam->is_public ? 'success' : 'secondary' }}">
                            <i class="bi bi-{{ $exam->is_public ? 'unlock' : 'lock' }}"></i>
                            {{ $exam->is_public ? 'Công khai' : 'Riêng tư' }}
                        </span>
                        @if($exam->attempts_count > 0)
                            <small class="text-muted">
                                <i class="bi bi-people"></i> {{ $exam->attempts_count }} lượt thi
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-item-detail {
    padding: 1rem;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    font-weight: 600;
}

.sticky-top {
    position: sticky;
}
</style>
@endsection
