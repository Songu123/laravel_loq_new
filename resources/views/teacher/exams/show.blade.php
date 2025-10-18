@extends('layouts.teacher-dashboard')

@section('title', $exam->title)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Đề thi</a></li>
        <li class="breadcrumb-item active">{{ $exam->title }}</li>
    </ol>
</nav>
@endsection

@section('teacher-dashboard-content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $exam->title }}</h1>
            <p class="text-muted">Chi tiết đề thi và câu hỏi</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('teacher.exams.edit', $exam) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Chỉnh sửa
            </a>
            <a href="{{ route('teacher.exams.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Exam Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-success">Thông tin đề thi</h6>
                </div>
                <div class="card-body">
                    <!-- Basic Info -->
                    <div class="mb-3">
                        <label class="form-label text-muted small">Tên đề thi</label>
                        <div class="fw-bold">{{ $exam->title }}</div>
                    </div>

                    @if($exam->description)
                        <div class="mb-3">
                            <label class="form-label text-muted small">Mô tả</label>
                            <div>{{ $exam->description }}</div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-muted small">Danh mục</label>
                        <div>
                            <span class="badge" style="background-color: {{ $exam->category->color }}20; color: {{ $exam->category->color }};">
                                <i class="{{ $exam->category->icon ?? 'bi-folder' }} me-1"></i>
                                {{ $exam->category->name }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small">Thời gian</label>
                            <div class="fw-bold">{{ $exam->duration_text }}</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small">Độ khó</label>
                            <div>
                                <span class="badge bg-{{ $exam->difficulty_level === 'easy' ? 'success' : ($exam->difficulty_level === 'medium' ? 'warning' : 'danger') }}">
                                    {{ $exam->difficulty_level_text }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Time Settings -->
                    @if($exam->start_time || $exam->end_time)
                        <div class="mb-3">
                            <label class="form-label text-muted small">Thời gian mở/đóng</label>
                            <div>
                                @if($exam->start_time)
                                    <div class="small">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        Bắt đầu: {{ $exam->start_time->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                                @if($exam->end_time)
                                    <div class="small">
                                        <i class="bi bi-calendar-x me-1"></i>
                                        Kết thúc: {{ $exam->end_time->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label text-muted small">Trạng thái</label>
                        <div>
                            @if($exam->is_active)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Hoạt động
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-pause-circle me-1"></i>Tạm dừng
                                </span>
                            @endif
                            
                            @if($exam->is_public)
                                <span class="badge bg-info">
                                    <i class="bi bi-globe me-1"></i>Công khai
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="bi bi-lock me-1"></i>Riêng tư
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Meta Info -->
                    <div class="border-top pt-3">
                        <div class="small text-muted">
                            <div class="mb-1">
                                <i class="bi bi-calendar me-1"></i>
                                Tạo: {{ $exam->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div>
                                <i class="bi bi-clock-history me-1"></i>
                                Cập nhật: {{ $exam->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-success">Thống kê</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number text-primary">{{ $exam->questions->count() }}</div>
                                <div class="stat-label">Câu hỏi</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number text-success">{{ $exam->total_marks }}</div>
                                <div class="stat-label">Tổng điểm</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number text-info">0</div>
                                <div class="stat-label">Lượt thi</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Question Types Breakdown -->
                    @php
                        $questionTypes = $exam->questions->groupBy('question_type');
                    @endphp
                    
                    @if($questionTypes->count() > 0)
                        <div class="border-top pt-3 mt-3">
                            <label class="form-label text-muted small">Loại câu hỏi</label>
                            @foreach($questionTypes as $type => $questions)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        @switch($type)
                                            @case('multiple_choice')
                                                <i class="bi bi-list-check me-1"></i>Trắc nghiệm
                                                @break
                                            @case('true_false')
                                                <i class="bi bi-check2-square me-1"></i>Đúng/Sai
                                                @break
                                            @case('short_answer')
                                                <i class="bi bi-pencil me-1"></i>Trả lời ngắn
                                                @break
                                            @case('essay')
                                                <i class="bi bi-file-text me-1"></i>Tự luận
                                                @break
                                        @endswitch
                                    </span>
                                    <span class="badge bg-light text-dark">{{ $questions->count() }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Questions -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-success">Câu hỏi ({{ $exam->questions->count() }})</h6>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick="toggleAllAnswers()">
                                <i class="bi bi-eye"></i> Hiện/Ẩn đáp án
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($exam->questions->count() > 0)
                        @foreach($exam->questions as $question)
                            <div class="question-card card mb-3" id="question-{{ $question->id }}">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <span class="badge bg-primary me-2">{{ $loop->iteration }}</span>
                                            <span class="badge bg-{{ $question->question_type === 'multiple_choice' ? 'info' : ($question->question_type === 'true_false' ? 'success' : ($question->question_type === 'short_answer' ? 'warning' : 'secondary')) }}">
                                                {{ $question->question_type_text }}
                                            </span>
                                            @if($question->is_required)
                                                <span class="badge bg-danger">Bắt buộc</span>
                                            @endif
                                        </h6>
                                        <span class="text-muted small">{{ $question->marks }} điểm</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Question Text -->
                                    <div class="mb-3">
                                        <div class="question-text">{{ $question->question_text }}</div>
                                    </div>

                                    <!-- Answers -->
                                    @if($question->answers->count() > 0)
                                        <div class="answers-section" style="display: none;">
                                            <label class="form-label text-muted small">Đáp án:</label>
                                            <div class="list-group list-group-flush">
                                                @foreach($question->answers as $answer)
                                                    <div class="list-group-item {{ $answer->is_correct ? 'list-group-item-success' : '' }} px-0 py-2">
                                                        <div class="d-flex align-items-center">
                                                            @if($answer->is_correct)
                                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                            @else
                                                                <i class="bi bi-circle me-2 text-muted"></i>
                                                            @endif
                                                            <span class="{{ $answer->is_correct ? 'fw-bold text-success' : '' }}">
                                                                {{ $answer->answer_text }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Explanation -->
                                    @if($question->explanation)
                                        <div class="explanation-section mt-3" style="display: none;">
                                            <label class="form-label text-muted small">Giải thích:</label>
                                            <div class="alert alert-info">
                                                <i class="bi bi-lightbulb me-2"></i>{{ $question->explanation }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-question-circle display-4 text-muted mb-3"></i>
                            <h6 class="text-muted">Chưa có câu hỏi nào</h6>
                            <p class="text-muted">Thêm câu hỏi để hoàn thiện đề thi của bạn.</p>
                            <a href="{{ route('teacher.exams.edit', $exam) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Thêm câu hỏi
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-item {
    padding: 1rem 0;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 600;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.question-card {
    border-left: 4px solid #10b981;
}

.question-text {
    font-size: 1.1rem;
    line-height: 1.6;
}

.list-group-item-success {
    background-color: #d1fae5;
    border-color: #a7f3d0;
}

.answers-section.show,
.explanation-section.show {
    display: block !important;
}
</style>

@endsection

@push('scripts')
<script>
function toggleAllAnswers() {
    const answersSections = document.querySelectorAll('.answers-section');
    const explanationSections = document.querySelectorAll('.explanation-section');
    
    const isVisible = answersSections[0] && answersSections[0].style.display !== 'none';
    
    answersSections.forEach(section => {
        section.style.display = isVisible ? 'none' : 'block';
    });
    
    explanationSections.forEach(section => {
        section.style.display = isVisible ? 'none' : 'block';
    });
}
</script>
@endpush
