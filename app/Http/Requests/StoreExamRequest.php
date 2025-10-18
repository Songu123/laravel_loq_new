<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isTeacher() || auth()->user()->isAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'randomize_questions' => 'boolean',
            'show_results' => 'boolean',
            
            // Questions validation
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'questions.*.marks' => 'nullable|numeric|min:0',
            'questions.*.is_required' => 'boolean',
            'questions.*.explanation' => 'nullable|string',
            
            // Answers validation (for multiple choice and true/false)
            'questions.*.answers' => 'nullable|array',
            'questions.*.answers.*.answer_text' => 'required_with:questions.*.answers|string',
            'questions.*.answers.*.is_correct' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tên đề thi là bắt buộc',
            'title.max' => 'Tên đề thi không được quá 255 ký tự',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'category_id.exists' => 'Danh mục không tồn tại',
            'difficulty_level.required' => 'Vui lòng chọn độ khó',
            'difficulty_level.in' => 'Độ khó không hợp lệ',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu',
            'questions.required' => 'Đề thi phải có ít nhất một câu hỏi',
            'questions.min' => 'Đề thi phải có ít nhất một câu hỏi',
            'questions.*.question_text.required' => 'Nội dung câu hỏi là bắt buộc',
            'questions.*.question_type.required' => 'Loại câu hỏi là bắt buộc',
            'questions.*.answers.required_if' => 'Câu hỏi trắc nghiệm phải có đáp án',
            'questions.*.correct_answer.required_if' => 'Vui lòng chọn đáp án đúng',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Prepare questions data
        $questions = $this->input('questions', []);
        
        foreach ($questions as $qIndex => $question) {
            // Convert is_required to boolean
            $questions[$qIndex]['is_required'] = isset($question['is_required']) && $question['is_required'] == '1';
            
            // Process answers if they exist
            if (isset($question['answers']) && is_array($question['answers'])) {
                foreach ($question['answers'] as $aIndex => $answer) {
                    // Convert is_correct to boolean
                    $questions[$qIndex]['answers'][$aIndex]['is_correct'] = 
                        isset($answer['is_correct']) && $answer['is_correct'] == '1';
                }
            }
        }
        
        $this->merge([
            'questions' => $questions,
            'is_active' => $this->has('is_active') && $this->input('is_active') == '1',
            'is_public' => $this->has('is_public') && $this->input('is_public') == '1',
            'randomize_questions' => $this->has('randomize_questions') && $this->input('randomize_questions') == '1',
            'show_results' => $this->has('show_results') && $this->input('show_results') == '1',
        ]);
    }
}