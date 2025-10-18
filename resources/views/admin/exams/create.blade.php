@extends('layouts.dashboard')

@section('title', 'Tạo đề thi mới')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Tạo đề thi mới</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Đề thi</a></li>
                    <li class="breadcrumb-item active">Tạo mới</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('admin.exams.store') }}" method="POST" id="examForm">
        @csrf
        
        <div class="row">
            <!-- Left Column - Exam Info -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin đề thi</h6>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Tên đề thi <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
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
                                      rows="3" 
                                      placeholder="Mô tả ngắn về đề thi...">{{ old('description') }}</textarea>
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
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Duration -->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="duration_minutes" class="form-label">Thời gian (phút) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('duration_minutes') is-invalid @enderror" 
                                           id="duration_minutes" 
                                           name="duration_minutes" 
                                           value="{{ old('duration_minutes', 60) }}" 
                                           min="1" 
                                           max="600" 
                                           required>
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Difficulty -->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="difficulty_level" class="form-label">Độ khó <span class="text-danger">*</span></label>
                                    <select class="form-select @error('difficulty_level') is-invalid @enderror" 
                                            id="difficulty_level" 
                                            name="difficulty_level" 
                                            required>
                                        <option value="easy" {{ old('difficulty_level') == 'easy' ? 'selected' : '' }}>Dễ</option>
                                        <option value="medium" {{ old('difficulty_level', 'medium') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                        <option value="hard" {{ old('difficulty_level') == 'hard' ? 'selected' : '' }}>Khó</option>
                                    </select>
                                    @error('difficulty_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Start Time -->
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Thời gian bắt đầu</label>
                            <input type="datetime-local" 
                                   class="form-control @error('start_time') is-invalid @enderror" 
                                   id="start_time" 
                                   name="start_time" 
                                   value="{{ old('start_time') }}">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div class="mb-3">
                            <label for="end_time" class="form-label">Thời gian kết thúc</label>
                            <input type="datetime-local" 
                                   class="form-control @error('end_time') is-invalid @enderror" 
                                   id="end_time" 
                                   name="end_time" 
                                   value="{{ old('end_time') }}">
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Settings -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
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
                                       {{ old('is_public') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_public">
                                    Công khai
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu đề thi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Questions -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Câu hỏi</h6>
                            <button type="button" class="btn btn-sm btn-success" id="addQuestion">
                                <i class="fas fa-plus"></i> Thêm câu hỏi
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="questionsContainer">
                            <!-- Questions will be added here -->
                        </div>
                        
                        <div id="noQuestions" class="text-center py-4">
                            <i class="fas fa-question-circle display-4 text-muted mb-3"></i>
                            <h6 class="text-muted">Chưa có câu hỏi nào</h6>
                            <p class="text-muted">Nhấn "Thêm câu hỏi" để bắt đầu tạo đề thi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Question Template -->
<template id="questionTemplate">
    <div class="question-item card mb-3" data-question-index="">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Câu hỏi <span class="question-number"></span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-question">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Question Text -->
            <div class="mb-3">
                <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                <textarea class="form-control question-text" name="questions[][question_text]" rows="2" required></textarea>
            </div>

            <div class="row">
                <!-- Question Type -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Loại câu hỏi <span class="text-danger">*</span></label>
                        <select class="form-select question-type" name="questions[][question_type]" required>
                            <option value="multiple_choice">Trắc nghiệm</option>
                            <option value="true_false">Đúng/Sai</option>
                            <option value="short_answer">Câu trả lời ngắn</option>
                            <option value="essay">Tự luận</option>
                        </select>
                    </div>
                </div>

                <!-- Marks -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Điểm <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="questions[][marks]" 
                               value="1" min="0.1" max="100" step="0.1" required>
                    </div>
                </div>
            </div>

            <!-- Explanation -->
            <div class="mb-3">
                <label class="form-label">Giải thích (tùy chọn)</label>
                <textarea class="form-control" name="questions[][explanation]" rows="2"></textarea>
            </div>

            <!-- Required -->
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="questions[][is_required]" checked>
                    <label class="form-check-label">Bắt buộc trả lời</label>
                </div>
            </div>

            <!-- Answers Container -->
            <div class="answers-container">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0">Đáp án</label>
                    <button type="button" class="btn btn-sm btn-outline-primary add-answer">
                        <i class="fas fa-plus"></i> Thêm đáp án
                    </button>
                </div>
                <div class="answers-list">
                    <!-- Answers will be added here -->
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Answer Template -->
<template id="answerTemplate">
    <div class="answer-item input-group mb-2">
        <div class="input-group-text">
            <input class="form-check-input answer-correct" type="checkbox" name="questions[][answers][][is_correct]">
        </div>
        <input type="text" class="form-control answer-text" name="questions[][answers][][answer_text]" placeholder="Nhập đáp án...">
        <button type="button" class="btn btn-outline-danger remove-answer">
            <i class="fas fa-times"></i>
        </button>
    </div>
</template>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let questionIndex = 0;
    
    const questionsContainer = document.getElementById('questionsContainer');
    const noQuestions = document.getElementById('noQuestions');
    const addQuestionBtn = document.getElementById('addQuestion');
    
    // Add question event
    addQuestionBtn.addEventListener('click', addQuestion);
    
    // Add first question by default
    addQuestion();
    
    function addQuestion() {
        const template = document.getElementById('questionTemplate');
        const clone = template.content.cloneNode(true);
        
        // Update question index
        const questionItem = clone.querySelector('.question-item');
        questionItem.dataset.questionIndex = questionIndex;
        
        // Update question number
        clone.querySelector('.question-number').textContent = questionIndex + 1;
        
        // Update form field names
        updateQuestionFieldNames(clone, questionIndex);
        
        // Add event listeners
        addQuestionEventListeners(clone);
        
        // Add to container
        questionsContainer.appendChild(clone);
        
        // Hide no questions message
        noQuestions.style.display = 'none';
        
        // Add first two answers for multiple choice
        const answersContainer = questionItem.querySelector('.answers-list');
        addAnswer(answersContainer, questionIndex);
        addAnswer(answersContainer, questionIndex);
        
        questionIndex++;
        
        updateQuestionNumbers();
    }
    
    function addQuestionEventListeners(questionElement) {
        // Remove question
        questionElement.querySelector('.remove-question').addEventListener('click', function() {
            const questionItem = this.closest('.question-item');
            questionItem.remove();
            updateQuestionNumbers();
            
            if (questionsContainer.children.length === 0) {
                noQuestions.style.display = 'block';
            }
        });
        
        // Question type change
        questionElement.querySelector('.question-type').addEventListener('change', function() {
            const questionItem = this.closest('.question-item');
            const answersContainer = questionItem.querySelector('.answers-container');
            const answersList = questionItem.querySelector('.answers-list');
            
            if (this.value === 'multiple_choice' || this.value === 'true_false') {
                answersContainer.style.display = 'block';
                
                // Clear existing answers
                answersList.innerHTML = '';
                
                if (this.value === 'true_false') {
                    // Add True/False answers
                    addTrueFalseAnswers(answersList, questionItem.dataset.questionIndex);
                } else {
                    // Add empty answers for multiple choice
                    addAnswer(answersList, questionItem.dataset.questionIndex);
                    addAnswer(answersList, questionItem.dataset.questionIndex);
                }
            } else {
                answersContainer.style.display = 'none';
            }
        });
        
        // Add answer
        questionElement.querySelector('.add-answer').addEventListener('click', function() {
            const questionItem = this.closest('.question-item');
            const answersList = questionItem.querySelector('.answers-list');
            addAnswer(answersList, questionItem.dataset.questionIndex);
        });
    }
    
    function addAnswer(answersContainer, questionIdx) {
        const template = document.getElementById('answerTemplate');
        const clone = template.content.cloneNode(true);
        
        // Update field names
        const correctInput = clone.querySelector('.answer-correct');
        const textInput = clone.querySelector('.answer-text');
        
        correctInput.name = `questions[${questionIdx}][answers][][is_correct]`;
        textInput.name = `questions[${questionIdx}][answers][][answer_text]`;
        
        // Add remove event
        clone.querySelector('.remove-answer').addEventListener('click', function() {
            this.closest('.answer-item').remove();
        });
        
        answersContainer.appendChild(clone);
    }
    
    function addTrueFalseAnswers(answersContainer, questionIdx) {
        // True answer
        const trueAnswer = document.getElementById('answerTemplate').content.cloneNode(true);
        trueAnswer.querySelector('.answer-text').value = 'Đúng';
        trueAnswer.querySelector('.answer-text').name = `questions[${questionIdx}][answers][][answer_text]`;
        trueAnswer.querySelector('.answer-correct').name = `questions[${questionIdx}][answers][][is_correct]`;
        trueAnswer.querySelector('.remove-answer').remove(); // Can't remove true/false answers
        answersContainer.appendChild(trueAnswer);
        
        // False answer
        const falseAnswer = document.getElementById('answerTemplate').content.cloneNode(true);
        falseAnswer.querySelector('.answer-text').value = 'Sai';
        falseAnswer.querySelector('.answer-text').name = `questions[${questionIdx}][answers][][answer_text]`;
        falseAnswer.querySelector('.answer-correct').name = `questions[${questionIdx}][answers][][is_correct]`;
        falseAnswer.querySelector('.remove-answer').remove(); // Can't remove true/false answers
        answersContainer.appendChild(falseAnswer);
    }
    
    function updateQuestionFieldNames(questionElement, index) {
        const inputs = questionElement.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            if (input.name && input.name.includes('questions[]')) {
                input.name = input.name.replace('questions[]', `questions[${index}]`);
            }
        });
    }
    
    function updateQuestionNumbers() {
        const questions = questionsContainer.querySelectorAll('.question-item');
        questions.forEach((question, index) => {
            question.querySelector('.question-number').textContent = index + 1;
            question.dataset.questionIndex = index;
            
            // Update field names
            const inputs = question.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                if (input.name && input.name.includes('questions[')) {
                    input.name = input.name.replace(/questions\[\d+\]/, `questions[${index}]`);
                }
            });
        });
    }
});
</script>
@endpush