@extends('layouts.student-dashboard')

@section('title', 'Câu trả lời sai')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.practice.index') }}">Luyện tập</a></li>
        <li class="breadcrumb-item active">Câu trả lời sai</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
.wrong-answer-card {
    transition: all 0.3s;
    border-left: 4px solid #dc3545;
}

.wrong-answer-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.answer-option {
    padding: 1rem;
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    transition: all 0.2s;
}

.answer-option.user-answer {
    background-color: #fff5f5;
    border-color: #dc3545;
}

.answer-option.correct-answer {
    background-color: #f0fdf4;
    border-color: #198754;
}

.question-text {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #212529;
}

.filter-card {
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
}
</style>
@endpush

@section('student-dashboard-content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: #dc3545;">
                <div class="card-body p-4 text-white">
                    <h2 class="mb-2">
                        <i class="bi bi-x-circle-fill me-2"></i>Câu trả lời sai
                    </h2>
                    <p class="mb-0" style="opacity: 0.9;">
                        Xem lại và luyện tập các câu hỏi bạn đã trả lời sai
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card filter-card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('student.practice.wrong-answers') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Chủ đề</label>
                            <select name="category" class="form-select">
                                <option value="">Tất cả chủ đề</option>
                                @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Loại câu hỏi</label>
                            <select name="type" class="form-select">
                                <option value="">Tất cả loại</option>
                                <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                                <option value="true_false" {{ request('type') == 'true_false' ? 'selected' : '' }}>Đúng/Sai</option>
                                <option value="short_answer" {{ request('type') == 'short_answer' ? 'selected' : '' }}>Trả lời ngắn</option>
                                <option value="essay" {{ request('type') == 'essay' ? 'selected' : '' }}>Tự luận</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-funnel"></i> Lọc
                            </button>
                            <a href="{{ route('student.practice.wrong-answers') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger">{{ $wrongAnswers->total() }}</h3>
                    <p class="text-muted mb-0 small">Tổng câu sai</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $wrongAnswers->total() > 0 ? round(($wrongAnswers->total() / ($wrongAnswers->total() + request('correct', 0))) * 100, 1) : 0 }}%</h3>
                    <p class="text-muted mb-0 small">Tỷ lệ sai</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <button class="btn btn-success" onclick="showPracticeModal('wrong')">
                        <i class="bi bi-play-circle me-1"></i> Luyện tập ngay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Wrong Answers List -->
    @if($wrongAnswers->count() > 0)
    <div class="row">
        @foreach($wrongAnswers as $index => $answer)
        <div class="col-12 mb-4">
            <div class="card wrong-answer-card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-danger me-2">Câu {{ $wrongAnswers->firstItem() + $index }}</span>
                            <span class="badge bg-secondary">{{ $answer->question->exam->category->name ?? 'N/A' }}</span>
                            <span class="badge bg-info text-white ms-1">
                                {{ $answer->question->question_type_text }}
                            </span>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> {{ $answer->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Question -->
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="bi bi-question-circle text-primary me-2"></i>Câu hỏi:</h5>
                        <div class="question-text">
                            {!! nl2br(e($answer->question->question_text)) !!}
                        </div>
                        
                        @if($answer->question->image_path)
                        <div class="mt-3">
                            <img src="{{ asset('storage/' . $answer->question->image_path) }}" 
                                 alt="Question Image" 
                                 class="img-fluid rounded"
                                 style="max-height: 300px;">
                        </div>
                        @endif
                    </div>

                    <!-- Options for Multiple Choice -->
                    @if(in_array($answer->question->question_type, ['multiple_choice', 'true_false']))
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="bi bi-list-ul text-primary me-2"></i>Các đáp án:</h6>
                            @if($answer->question->answers && $answer->question->answers->count() > 0)
                                @foreach($answer->question->answers as $option)
                                <div class="answer-option 
                                    {{ $answer->answer_id == $option->id ? 'user-answer' : '' }}
                                    {{ $option->is_correct ? 'correct-answer' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{ $option->answer_text }}
                                        </div>
                                        <div>
                                            @if($answer->answer_id == $option->id)
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle"></i> Bạn đã chọn
                                                </span>
                                            @endif
                                            @if($option->is_correct)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Đáp án đúng
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="alert alert-warning">Không có đáp án cho câu hỏi này.</div>
                            @endif
                        </div>
                    @else
                        <!-- Answer for Short Answer/Essay -->
                        <div class="mb-3">
                            <h6 class="text-danger"><i class="bi bi-x-circle me-2"></i>Câu trả lời của bạn:</h6>
                            <div class="answer-option user-answer">
                                {{ $answer->answer_text ?? 'Không có câu trả lời' }}
                            </div>
                        </div>
                        
                        @php
                            $correctAnswer = $answer->question->answers->firstWhere('is_correct', true);
                        @endphp
                        <div class="mb-3">
                            <h6 class="text-success"><i class="bi bi-check-circle me-2"></i>Đáp án đúng:</h6>
                            <div class="answer-option correct-answer">
                                {{ $correctAnswer ? $correctAnswer->answer_text : 'N/A' }}
                            </div>
                        </div>
                    @endif

                    <!-- Explanation -->
                    @if($answer->question->explanation)
                    <div class="alert alert-info mb-0">
                        <h6 class="alert-heading">
                            <i class="bi bi-lightbulb me-2"></i>Giải thích:
                        </h6>
                        <p class="mb-0">{{ $answer->question->explanation }}</p>
                    </div>
                    @endif
                </div>
                
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">
                                <i class="bi bi-book"></i> Đề thi: {{ $answer->question->exam->title }}
                            </span>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="practiceThisQuestion({{ $answer->question->id }})">
                            <i class="bi bi-arrow-repeat"></i> Luyện tập câu này
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $wrongAnswers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-emoji-smile text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 mb-2">Tuyệt vời!</h4>
                    <p class="text-muted mb-4">
                        @if(request()->has('category') || request()->has('type'))
                            Không tìm thấy câu sai với bộ lọc này.
                        @else
                            Bạn chưa có câu trả lời sai nào. Hãy tiếp tục làm bài thi nhé!
                        @endif
                    </p>
                    <a href="{{ route('student.exams.index') }}" class="btn btn-primary">
                        <i class="bi bi-file-earmark-text"></i> Xem đề thi
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Practice Modal -->
<div class="modal fade" id="practiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-play-circle me-2"></i>Luyện tập câu sai</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('student.practice.start') }}" method="GET">
                <div class="modal-body">
                    <input type="hidden" name="mode" value="wrong">
                    
                    <div class="mb-3">
                        <label class="form-label">Số lượng câu hỏi</label>
                        <select name="limit" class="form-select">
                            <option value="5">5 câu</option>
                            <option value="10" selected>10 câu</option>
                            <option value="15">15 câu</option>
                            <option value="20">20 câu</option>
                            <option value="all">Tất cả</option>
                        </select>
                    </div>
                    
                    @if(request()->has('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    
                    @if(request()->has('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Bạn sẽ luyện tập với các câu hỏi đã trả lời sai để cải thiện kết quả</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-play-fill me-1"></i>Bắt đầu luyện tập
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let practiceModal;
document.addEventListener('DOMContentLoaded', function() {
    practiceModal = new bootstrap.Modal(document.getElementById('practiceModal'));
});

function showPracticeModal(mode) {
    practiceModal.show();
}

function practiceThisQuestion(questionId) {
    window.location.href = "{{ route('student.practice.start') }}?mode=question&question_id=" + questionId;
}
</script>
@endpush
