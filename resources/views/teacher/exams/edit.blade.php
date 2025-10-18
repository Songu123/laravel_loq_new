@extends('layouts.teacher-dashboard')

@section('title', 'Chỉnh sửa đề thi')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Đề thi</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa: {{ $exam->title }}</li>
    </ol>
</nav>
@endsection

@section('teacher-dashboard-content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa đề thi</h1>
            <p class="text-muted">Cập nhật thông tin đề thi và câu hỏi</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-info">
                <i class="bi bi-eye"></i> Xem trước
            </a>
            <a href="{{ route('teacher.exams.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6><i class="bi bi-exclamation-triangle me-2"></i>Có lỗi xảy ra:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('teacher.exams.update', $exam) }}" method="POST" id="examForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Exam Basic Info -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-warning">Thông tin cơ bản</h6>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Tên đề thi <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $exam->title) }}" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $exam->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id" 
                                    required>
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $exam->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div class="mb-3">
                            <label for="duration_minutes" class="form-label">Thời gian làm bài (phút)</label>
                            <input type="number" 
                                   class="form-control @error('duration_minutes') is-invalid @enderror" 
                                   id="duration_minutes" 
                                   name="duration_minutes" 
                                   value="{{ old('duration_minutes', $exam->duration_minutes) }}" 
                                   min="1">
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Difficulty -->
                        <div class="mb-3">
                            <label for="difficulty_level" class="form-label">Độ khó</label>
                            <select class="form-select @error('difficulty_level') is-invalid @enderror" 
                                    id="difficulty_level" 
                                    name="difficulty_level">
                                <option value="easy" {{ old('difficulty_level', $exam->difficulty_level) == 'easy' ? 'selected' : '' }}>Dễ</option>
                                <option value="medium" {{ old('difficulty_level', $exam->difficulty_level) == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="hard" {{ old('difficulty_level', $exam->difficulty_level) == 'hard' ? 'selected' : '' }}>Khó</option>
                            </select>
                            @error('difficulty_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-warning">Cài đặt</h6>
                    </div>
                    <div class="card-body">
                        <!-- Timing -->
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Thời gian bắt đầu</label>
                            <input type="datetime-local" 
                                   class="form-control @error('start_time') is-invalid @enderror" 
                                   id="start_time" 
                                   name="start_time" 
                                   value="{{ old('start_time', $exam->start_time?->format('Y-m-d\TH:i')) }}">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="end_time" class="form-label">Thời gian kết thúc</label>
                            <input type="datetime-local" 
                                   class="form-control @error('end_time') is-invalid @enderror" 
                                   id="end_time" 
                                   name="end_time" 
                                   value="{{ old('end_time', $exam->end_time?->format('Y-m-d\TH:i')) }}">
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Toggles -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $exam->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Kích hoạt đề thi
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_public" 
                                       name="is_public" 
                                       value="1" 
                                       {{ old('is_public', $exam->is_public) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_public">
                                    Công khai
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="randomize_questions" 
                                       name="randomize_questions" 
                                       value="1" 
                                       {{ old('randomize_questions', $exam->randomize_questions ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="randomize_questions">
                                    Xáo trộn câu hỏi
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="show_results" 
                                       name="show_results" 
                                       value="1" 
                                       {{ old('show_results', $exam->show_results ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_results">
                                    Hiện kết quả sau khi thi
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-save"></i> Cập nhật đề thi
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="window.history.back()">
                            <i class="bi bi-x-circle"></i> Hủy bỏ
                        </button>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-warning">Câu hỏi</h6>
                            <button type="button" class="btn btn-warning btn-sm" onclick="addQuestion()">
                                <i class="bi bi-plus-circle"></i> Thêm câu hỏi
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="questionsContainer">
                            @foreach($exam->questions as $index => $question)
                                <div class="question-item card mb-3" data-question-index="{{ $index }}">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Câu hỏi {{ $index + 1 }}</h6>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Question Text -->
                                        <div class="mb-3">
                                            <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                                            <textarea class="form-control" 
                                                      name="questions[{{ $index }}][question_text]" 
                                                      rows="2" 
                                                      required>{{ old("questions.{$index}.question_text", $question->question_text) }}</textarea>
                                        </div>

                                        <div class="row">
                                            <!-- Question Type -->
                                            <div class="col-md-4">
                                                <label class="form-label">Loại câu hỏi</label>
                                                <select class="form-select question-type" 
                                                        name="questions[{{ $index }}][question_type]" 
                                                        onchange="updateAnswersSection(this)">
                                                    <option value="multiple_choice" {{ old("questions.{$index}.question_type", $question->question_type) == 'multiple_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                                                    <option value="true_false" {{ old("questions.{$index}.question_type", $question->question_type) == 'true_false' ? 'selected' : '' }}>Đúng/Sai</option>
                                                    <option value="short_answer" {{ old("questions.{$index}.question_type", $question->question_type) == 'short_answer' ? 'selected' : '' }}>Trả lời ngắn</option>
                                                    <option value="essay" {{ old("questions.{$index}.question_type", $question->question_type) == 'essay' ? 'selected' : '' }}>Tự luận</option>
                                                </select>
                                            </div>

                                            <!-- Marks -->
                                            <div class="col-md-4">
                                                <label class="form-label">Điểm</label>
                                                <input type="number" 
                                                       class="form-control" 
                                                       name="questions[{{ $index }}][marks]" 
                                                       value="{{ old("questions.{$index}.marks", $question->marks) }}" 
                                                       min="0" 
                                                       step="0.1">
                                            </div>

                                            <!-- Required -->
                                            <div class="col-md-4">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="questions[{{ $index }}][is_required]" 
                                                           value="1" 
                                                           {{ old("questions.{$index}.is_required", $question->is_required) ? 'checked' : '' }}>
                                                    <label class="form-check-label">Bắt buộc</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Answers Section -->
                                        <div class="answers-section mt-3" style="{{ in_array($question->question_type, ['short_answer', 'essay']) ? 'display: none;' : '' }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">Đáp án</label>
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addAnswer(this)">
                                                    <i class="bi bi-plus"></i> Thêm
                                                </button>
                                            </div>
                                            <div class="answers-container">
                                                @if($question->question_type === 'true_false')
                                                    <!-- True/False answers -->
                                                    @php
                                                        $trueAnswer = $question->answers->where('answer_text', 'Đúng')->first();
                                                        $falseAnswer = $question->answers->where('answer_text', 'Sai')->first();
                                                    @endphp
                                                    <div class="answer-item d-flex align-items-center mb-2">
                                                        <div class="form-check me-3">
                                                            <input class="form-check-input" 
                                                                   type="radio" 
                                                                   name="questions[{{ $index }}][correct_answer]" 
                                                                   value="0" 
                                                                   {{ ($trueAnswer && $trueAnswer->is_correct) ? 'checked' : '' }}>
                                                        </div>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="questions[{{ $index }}][answers][0][answer_text]" 
                                                               value="Đúng" 
                                                               readonly>
                                                    </div>
                                                    <div class="answer-item d-flex align-items-center mb-2">
                                                        <div class="form-check me-3">
                                                            <input class="form-check-input" 
                                                                   type="radio" 
                                                                   name="questions[{{ $index }}][correct_answer]" 
                                                                   value="1" 
                                                                   {{ ($falseAnswer && $falseAnswer->is_correct) ? 'checked' : '' }}>
                                                        </div>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="questions[{{ $index }}][answers][1][answer_text]" 
                                                               value="Sai" 
                                                               readonly>
                                                    </div>
                                                @else
                                                    <!-- Multiple choice answers -->
                                                    @foreach($question->answers as $answerIndex => $answer)
                                                        <div class="answer-item d-flex align-items-center mb-2">
                                                            <div class="form-check me-3">
                                                                <input class="form-check-input" 
                                                                       type="radio" 
                                                                       name="questions[{{ $index }}][correct_answer]" 
                                                                       value="{{ $answerIndex }}" 
                                                                       {{ $answer->is_correct ? 'checked' : '' }}>
                                                            </div>
                                                            <input type="text" 
                                                                   class="form-control me-2" 
                                                                   name="questions[{{ $index }}][answers][{{ $answerIndex }}][answer_text]" 
                                                                   value="{{ old("questions.{$index}.answers.{$answerIndex}.answer_text", $answer->answer_text) }}" 
                                                                   placeholder="Nhập đáp án">
                                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeAnswer(this)">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Explanation -->
                                        <div class="mt-3">
                                            <label class="form-label">Giải thích (tùy chọn)</label>
                                            <textarea class="form-control" 
                                                      name="questions[{{ $index }}][explanation]" 
                                                      rows="2">{{ old("questions.{$index}.explanation", $question->explanation) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($exam->questions->count() == 0)
                            <div class="text-center py-4" id="noQuestionsMessage">
                                <i class="bi bi-question-circle display-4 text-muted mb-3"></i>
                                <h6 class="text-muted">Chưa có câu hỏi nào</h6>
                                <p class="text-muted">Nhấn "Thêm câu hỏi" để bắt đầu.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Question Template -->
<template id="questionTemplate">
    <div class="question-item card mb-3" data-question-index="">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Câu hỏi</h6>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Question Text -->
            <div class="mb-3">
                <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                <textarea class="form-control" name="questions[][question_text]" rows="2" required></textarea>
            </div>

            <div class="row">
                <!-- Question Type -->
                <div class="col-md-4">
                    <label class="form-label">Loại câu hỏi</label>
                    <select class="form-select question-type" name="questions[][question_type]" onchange="updateAnswersSection(this)">
                        <option value="multiple_choice">Trắc nghiệm</option>
                        <option value="true_false">Đúng/Sai</option>
                        <option value="short_answer">Trả lời ngắn</option>
                        <option value="essay">Tự luận</option>
                    </select>
                </div>

                <!-- Marks -->
                <div class="col-md-4">
                    <label class="form-label">Điểm</label>
                    <input type="number" class="form-control" name="questions[][marks]" value="1" min="0" step="0.1">
                </div>

                <!-- Required -->
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="questions[][is_required]" value="1">
                        <label class="form-check-label">Bắt buộc</label>
                    </div>
                </div>
            </div>

            <!-- Answers Section -->
            <div class="answers-section mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0">Đáp án</label>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addAnswer(this)">
                        <i class="bi bi-plus"></i> Thêm
                    </button>
                </div>
                <div class="answers-container">
                    <div class="answer-item d-flex align-items-center mb-2">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="questions[][correct_answer]" value="0" checked>
                        </div>
                        <input type="text" class="form-control me-2" name="questions[][answers][0][answer_text]" placeholder="Nhập đáp án">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeAnswer(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Explanation -->
            <div class="mt-3">
                <label class="form-label">Giải thích (tùy chọn)</label>
                <textarea class="form-control" name="questions[][explanation]" rows="2"></textarea>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
let questionIndex = {{ $exam->questions->count() }};

function addQuestion() {
    const template = document.getElementById('questionTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update question index
    const questionItem = clone.querySelector('.question-item');
    questionItem.dataset.questionIndex = questionIndex;
    
    // Update question title
    clone.querySelector('h6').textContent = `Câu hỏi ${questionIndex + 1}`;
    
    // Update field names
    updateFieldNames(clone, questionIndex);
    
    // Add to container
    document.getElementById('questionsContainer').appendChild(clone);
    
    // Hide no questions message
    const noQuestionsMessage = document.getElementById('noQuestionsMessage');
    if (noQuestionsMessage) {
        noQuestionsMessage.style.display = 'none';
    }
    
    questionIndex++;
    updateQuestionNumbers();
}

function removeQuestion(button) {
    button.closest('.question-item').remove();
    updateQuestionNumbers();
    
    // Show no questions message if no questions left
    const questionsContainer = document.getElementById('questionsContainer');
    if (questionsContainer.children.length === 0) {
        const noQuestionsMessage = document.getElementById('noQuestionsMessage');
        if (noQuestionsMessage) {
            noQuestionsMessage.style.display = 'block';
        }
    }
}

function updateQuestionNumbers() {
    const questions = document.querySelectorAll('.question-item');
    questions.forEach((question, index) => {
        question.dataset.questionIndex = index;
        question.querySelector('h6').textContent = `Câu hỏi ${index + 1}`;
        updateFieldNames(question, index);
    });
    questionIndex = questions.length;
}

function updateFieldNames(container, index) {
    const inputs = container.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        if (input.name) {
            input.name = input.name.replace(/questions\[\d*\]/, `questions[${index}]`);
        }
    });
}

function updateAnswersSection(select) {
    const questionItem = select.closest('.question-item');
    const answersSection = questionItem.querySelector('.answers-section');
    const answersContainer = questionItem.querySelector('.answers-container');
    const questionIndex = questionItem.dataset.questionIndex;
    
    if (select.value === 'short_answer' || select.value === 'essay') {
        answersSection.style.display = 'none';
    } else {
        answersSection.style.display = 'block';
        
        if (select.value === 'true_false') {
            // Create True/False answers
            answersContainer.innerHTML = `
                <div class="answer-item d-flex align-items-center mb-2">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="questions[${questionIndex}][correct_answer]" value="0" checked>
                    </div>
                    <input type="text" class="form-control" name="questions[${questionIndex}][answers][0][answer_text]" value="Đúng" readonly>
                </div>
                <div class="answer-item d-flex align-items-center mb-2">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="questions[${questionIndex}][correct_answer]" value="1">
                    </div>
                    <input type="text" class="form-control" name="questions[${questionIndex}][answers][1][answer_text]" value="Sai" readonly>
                </div>
            `;
        } else if (select.value === 'multiple_choice') {
            // Create multiple choice answers if container is empty
            if (answersContainer.children.length === 0) {
                answersContainer.innerHTML = `
                    <div class="answer-item d-flex align-items-center mb-2">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="questions[${questionIndex}][correct_answer]" value="0" checked>
                        </div>
                        <input type="text" class="form-control me-2" name="questions[${questionIndex}][answers][0][answer_text]" placeholder="Nhập đáp án">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeAnswer(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
            }
        }
    }
}

function addAnswer(button) {
    const answersContainer = button.parentElement.nextElementSibling;
    const questionItem = button.closest('.question-item');
    const questionIndex = questionItem.dataset.questionIndex;
    const answerIndex = answersContainer.children.length;
    
    const answerHtml = `
        <div class="answer-item d-flex align-items-center mb-2">
            <div class="form-check me-3">
                <input class="form-check-input" type="radio" name="questions[${questionIndex}][correct_answer]" value="${answerIndex}">
            </div>
            <input type="text" class="form-control me-2" name="questions[${questionIndex}][answers][${answerIndex}][answer_text]" placeholder="Nhập đáp án">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeAnswer(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    
    answersContainer.insertAdjacentHTML('beforeend', answerHtml);
}

function removeAnswer(button) {
    const answerItem = button.closest('.answer-item');
    const answersContainer = answerItem.parentElement;
    
    // Don't remove if it's the last answer
    if (answersContainer.children.length <= 1) {
        alert('Phải có ít nhất một đáp án');
        return;
    }
    
    answerItem.remove();
    
    // Update answer indices
    const questionItem = button.closest('.question-item');
    const questionIndex = questionItem.dataset.questionIndex;
    const answers = answersContainer.querySelectorAll('.answer-item');
    
    answers.forEach((answer, index) => {
        const radio = answer.querySelector('input[type="radio"]');
        const textInput = answer.querySelector('input[type="text"]');
        
        radio.value = index;
        radio.name = `questions[${questionIndex}][correct_answer]`;
        textInput.name = `questions[${questionIndex}][answers][${index}][answer_text]`;
    });
}

// Form validation
document.getElementById('examForm').addEventListener('submit', function(e) {
    const questions = document.querySelectorAll('.question-item');
    
    if (questions.length === 0) {
        e.preventDefault();
        alert('Vui lòng thêm ít nhất một câu hỏi');
        return;
    }
    
    // Validate each question
    for (let question of questions) {
        const questionText = question.querySelector('textarea[name*="[question_text]"]');
        if (!questionText.value.trim()) {
            e.preventDefault();
            alert('Vui lòng nhập nội dung cho tất cả câu hỏi');
            questionText.focus();
            return;
        }
        
        const questionType = question.querySelector('select[name*="[question_type]"]');
        if (questionType.value === 'multiple_choice' || questionType.value === 'true_false') {
            const answers = question.querySelectorAll('input[name*="[answer_text]"]');
            let hasValidAnswer = false;
            
            for (let answer of answers) {
                if (answer.value.trim()) {
                    hasValidAnswer = true;
                    break;
                }
            }
            
            if (!hasValidAnswer) {
                e.preventDefault();
                alert('Vui lòng nhập ít nhất một đáp án cho câu hỏi trắc nghiệm');
                return;
            }
            
            const correctAnswer = question.querySelector('input[name*="[correct_answer]"]:checked');
            if (!correctAnswer) {
                e.preventDefault();
                alert('Vui lòng chọn đáp án đúng cho câu hỏi');
                return;
            }
        }
    }
});
</script>
@endpush
