<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AIQuestionImportController extends Controller
{
    /**
     * Show upload PDF form
     */
    public function showUploadForm()
    {
        $categories = Category::where('created_by', auth()->id())
            ->orderBy('name')
            ->get();

        return view('teacher.ai-import.upload', compact('categories'));
    }

    /**
     * Upload PDF and extract questions using Python AI service
     */
    public function uploadPDF(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
            'category_id' => 'required|exists:categories,id',
            'language' => 'required|in:vie,eng',
            'default_difficulty' => 'required|in:easy,medium,hard'
        ]);

        try {
            // Check if Python API is running
            $pythonApiUrl = config('services.python_api.url');
            
            if (!$pythonApiUrl) {
                return back()->with('error', 'Python API URL chưa được cấu hình. Vui lòng kiểm tra file .env');
            }

            // Upload PDF to Python API
            $pdfFile = $request->file('pdf_file');
            
            $response = Http::timeout(120) // 2 minutes timeout for large PDFs
                ->attach('file', file_get_contents($pdfFile->getRealPath()), $pdfFile->getClientOriginalName())
                ->post("{$pythonApiUrl}/api/extract-questions", [
                    'language' => $request->language
                ]);

            if (!$response->successful()) {
                Log::error('Python API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return back()->with('error', 'Không thể kết nối đến AI service. Vui lòng kiểm tra Python API đang chạy.');
            }

            $data = $response->json();

            if (!$data['success'] || empty($data['questions'])) {
                return back()->with('warning', 'Không tìm thấy câu hỏi trong file PDF. Vui lòng kiểm tra định dạng file.');
            }

            // Store questions temporarily in session for review
            $questions = $data['questions'];
            
            // Add default values from form
            foreach ($questions as &$question) {
                $question['category_id'] = $request->category_id;
                $question['difficulty'] = $question['difficulty'] ?? $request->default_difficulty;
            }

            session([
                'ai_imported_questions' => $questions,
                'ai_import_metadata' => $data['metadata'] ?? []
            ]);

            return redirect()->route('teacher.ai-import.review')
                ->with('success', "Đã trích xuất được {$data['total_questions']} câu hỏi. Vui lòng kiểm tra và chỉnh sửa trước khi lưu.");

        } catch (\Exception $e) {
            Log::error('AI Import Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show review page for extracted questions
     */
    public function reviewQuestions()
    {
        $questions = session('ai_imported_questions');
        $metadata = session('ai_import_metadata');

        if (!$questions) {
            return redirect()->route('teacher.ai-import.form')
                ->with('error', 'Không có dữ liệu để xem. Vui lòng upload PDF trước.');
        }

        $categories = Category::where('created_by', auth()->id())
            ->orderBy('name')
            ->get();

        return view('teacher.ai-import.review', compact('questions', 'metadata', 'categories'));
    }

    /**
     * Save reviewed questions to database
     */
    public function saveQuestions(Request $request)
    {
        $request->validate([
            // Exam data
            'exam_title' => 'required|string|max:255',
            'exam_description' => 'nullable|string',
            'exam_duration' => 'required|integer|min:5|max:300',
            'exam_total_marks' => 'required|numeric|min:1|max:1000',
            'exam_status' => 'required|in:draft,published',
            'exam_category_id' => 'required|exists:categories,id', // Category cho exam
            
            // Questions data
            'questions' => 'required|array|min:1',
            'questions.*.content' => 'required|string',
            'questions.*.category_id' => 'required|exists:categories,id',
            'questions.*.difficulty' => 'required|in:easy,medium,hard',
            'questions.*.answers' => 'required|array|min:1',
            'questions.*.answers.*.content' => 'required|string',
            'questions.*.answers.*.is_correct' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Exam first
            $exam = Exam::create([
                'title' => $request->exam_title,
                'description' => $request->exam_description,
                'category_id' => $request->exam_category_id,
                'duration_minutes' => $request->exam_duration,
                'total_marks' => $request->exam_total_marks,
                'is_active' => $request->exam_status === 'published',
                'is_public' => $request->exam_status === 'published',
                'created_by' => auth()->id(),
                'difficulty_level' => 'medium', // Default
            ]);

            $savedCount = 0;
            $errors = [];
            $questionOrder = 1;

            // 2. Create Questions and attach to Exam
            foreach ($request->questions as $index => $questionData) {
                // Skip if unchecked
                if (!isset($questionData['selected']) || !$questionData['selected']) {
                    continue;
                }

                // Verify user owns the category
                $category = Category::where('id', $questionData['category_id'])
                    ->where('created_by', auth()->id())
                    ->first();

                if (!$category) {
                    $errors[] = "Câu hỏi #" . ($index + 1) . ": Danh mục không hợp lệ";
                    continue;
                }
                
                // Validate at least one correct answer
                $hasCorrectAnswer = false;
                foreach ($questionData['answers'] as $answer) {
                    if (isset($answer['is_correct']) && $answer['is_correct']) {
                        $hasCorrectAnswer = true;
                        break;
                    }
                }
                
                if (!$hasCorrectAnswer) {
                    $errors[] = "Câu hỏi #" . ($index + 1) . ": Phải có ít nhất 1 đáp án đúng";
                    continue;
                }

                // Calculate marks per question (evenly distributed)
                $marksPerQuestion = round($request->exam_total_marks / count(array_filter($request->questions, function($q) {
                    return isset($q['selected']) && $q['selected'];
                })), 2);

                // Create question with exam_id
                $question = Question::create([
                    'exam_id' => $exam->id,
                    'question_text' => $questionData['content'],
                    'question_type' => 'multiple_choice',
                    'marks' => $marksPerQuestion,
                    'order' => $questionOrder++,
                    'is_required' => true,
                    'explanation' => null,
                ]);

                // Create answers
                $answerOrder = 1;
                foreach ($questionData['answers'] as $answerData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $answerData['content'],
                        'is_correct' => $answerData['is_correct'] ?? false,
                        'order' => $answerOrder++,
                    ]);
                }

                $savedCount++;
            }

            // Update exam total questions count
            $exam->update([
                'total_questions' => $savedCount
            ]);

            DB::commit();

            // Clear session
            session()->forget(['ai_imported_questions', 'ai_import_metadata']);

            if ($savedCount > 0) {
                $message = "Đã tạo đề thi '{$exam->title}' với {$savedCount} câu hỏi thành công!";
                if (!empty($errors)) {
                    $message .= " Có " . count($errors) . " lỗi: " . implode(', ', $errors);
                }
                return redirect()->route('teacher.exams.show', $exam->id)
                    ->with('success', $message);
            } else {
                // Delete exam if no questions saved
                $exam->delete();
                return back()->with('error', 'Không có câu hỏi nào được lưu. ' . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save AI Questions Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Có lỗi khi lưu câu hỏi: ' . $e->getMessage());
        }
    }

    /**
     * Cancel import and clear session
     */
    public function cancelImport()
    {
        session()->forget(['ai_imported_questions', 'ai_import_metadata']);
        
        return redirect()->route('teacher.ai-import.form')
            ->with('info', 'Đã hủy import câu hỏi.');
    }
}
