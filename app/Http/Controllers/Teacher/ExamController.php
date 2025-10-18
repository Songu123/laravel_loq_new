<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Category;
use App\Models\Question;
use App\Models\Answer;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->middleware('auth');
        $this->middleware('teacher');
        $this->examService = $examService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['category', 'creator'])
                     ->withCount('questions')
                     ->where('created_by', Auth::id());

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $exams = $query->paginate(12)->appends($request->query());
        $categories = Category::active()->orderBy('name')->get();

        return view('teacher.exams.index', compact('exams', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('teacher.exams.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'duration_minutes' => 'required|integer|min:1|max:600',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'is_public' => 'boolean',
            'start_time' => 'nullable|date|after:now',
            'end_time' => 'nullable|date|after:start_time',
            
            // Questions
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'questions.*.marks' => 'required|numeric|min:0.1|max:100',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.is_required' => 'boolean',
            
            // Answers
            'questions.*.answers' => 'required_if:questions.*.question_type,multiple_choice,true_false|array',
            'questions.*.answers.*.answer_text' => 'required_with:questions.*.answers|string',
            'questions.*.answers.*.is_correct' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Create exam
            $exam = Exam::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'created_by' => Auth::id(),
                'duration_minutes' => $validated['duration_minutes'],
                'difficulty_level' => $validated['difficulty_level'],
                'is_active' => true, // Teacher exams are active by default
                'is_public' => $request->boolean('is_public'),
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
            ]);

            // Create questions and answers
            foreach ($validated['questions'] as $index => $questionData) {
                $question = Question::create([
                    'exam_id' => $exam->id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'marks' => $questionData['marks'],
                    'order' => $index + 1,
                    'explanation' => $questionData['explanation'] ?? null,
                    'is_required' => $questionData['is_required'] ?? true,
                ]);

                // Create answers for multiple choice and true/false questions
                if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                    foreach ($questionData['answers'] as $answerIndex => $answerData) {
                        if (!empty($answerData['answer_text'])) {
                            Answer::create([
                                'question_id' => $question->id,
                                'answer_text' => $answerData['answer_text'],
                                'is_correct' => $answerData['is_correct'] ?? false,
                                'order' => $answerIndex + 1,
                            ]);
                        }
                    }
                }
            }

            // Update exam stats
            $exam->updateStats();

            DB::commit();

            return redirect()->route('teacher.exams.show', $exam)
                           ->with('success', 'Đề thi đã được tạo thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => 'Có lỗi xảy ra khi tạo đề thi: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        // Check if user can view this exam
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đề thi này.');
        }

        $exam->load(['category', 'creator', 'questions.answers']);
        
        return view('teacher.exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        // Check if user can edit this exam
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa đề thi này.');
        }

        $exam->load(['questions.answers']);
        $categories = Category::active()->orderBy('name')->get();
        
        return view('teacher.exams.edit', compact('exam', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        // Check if user can edit this exam
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa đề thi này.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'duration_minutes' => 'required|integer|min:1|max:600',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'is_public' => 'boolean',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            
            // Questions
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|exists:questions,id',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'questions.*.marks' => 'required|numeric|min:0.1|max:100',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.is_required' => 'boolean',
            
            // Answers
            'questions.*.answers' => 'required_if:questions.*.question_type,multiple_choice,true_false|array',
            'questions.*.answers.*.id' => 'nullable|exists:answers,id',
            'questions.*.answers.*.answer_text' => 'required_with:questions.*.answers|string',
            'questions.*.answers.*.is_correct' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Update exam
            $exam->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'duration_minutes' => $validated['duration_minutes'],
                'difficulty_level' => $validated['difficulty_level'],
                'is_public' => $request->boolean('is_public'),
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
            ]);

            // Get existing question IDs
            $existingQuestionIds = $exam->questions->pluck('id')->toArray();
            $submittedQuestionIds = [];

            // Update/Create questions and answers
            foreach ($validated['questions'] as $index => $questionData) {
                if (isset($questionData['id']) && $questionData['id']) {
                    // Update existing question
                    $question = Question::find($questionData['id']);
                    $submittedQuestionIds[] = $question->id;
                } else {
                    // Create new question
                    $question = new Question();
                    $question->exam_id = $exam->id;
                }

                $question->fill([
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'marks' => $questionData['marks'],
                    'order' => $index + 1,
                    'explanation' => $questionData['explanation'] ?? null,
                    'is_required' => $questionData['is_required'] ?? true,
                ]);
                $question->save();

                if (!in_array($question->id, $submittedQuestionIds)) {
                    $submittedQuestionIds[] = $question->id;
                }

                // Handle answers
                if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                    $existingAnswerIds = $question->answers->pluck('id')->toArray();
                    $submittedAnswerIds = [];

                    foreach ($questionData['answers'] as $answerIndex => $answerData) {
                        if (!empty($answerData['answer_text'])) {
                            if (isset($answerData['id']) && $answerData['id']) {
                                // Update existing answer
                                $answer = Answer::find($answerData['id']);
                                $submittedAnswerIds[] = $answer->id;
                            } else {
                                // Create new answer
                                $answer = new Answer();
                                $answer->question_id = $question->id;
                            }

                            $answer->fill([
                                'answer_text' => $answerData['answer_text'],
                                'is_correct' => $answerData['is_correct'] ?? false,
                                'order' => $answerIndex + 1,
                            ]);
                            $answer->save();

                            if (!in_array($answer->id, $submittedAnswerIds)) {
                                $submittedAnswerIds[] = $answer->id;
                            }
                        }
                    }

                    // Delete removed answers
                    $answersToDelete = array_diff($existingAnswerIds, $submittedAnswerIds);
                    Answer::whereIn('id', $answersToDelete)->delete();
                }
            }

            // Delete removed questions
            $questionsToDelete = array_diff($existingQuestionIds, $submittedQuestionIds);
            Question::whereIn('id', $questionsToDelete)->delete();

            // Update exam stats
            $exam->updateStats();

            DB::commit();

            return redirect()->route('teacher.exams.show', $exam)
                           ->with('success', 'Đề thi đã được cập nhật thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật đề thi: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        // Check if user can delete this exam
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa đề thi này.');
        }

        try {
            $exam->delete();
            return redirect()->route('teacher.exams.index')
                           ->with('success', 'Đề thi đã được xóa thành công!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi xóa đề thi: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle exam status
     */
    public function toggleStatus(Exam $exam)
    {
        // Check if user can edit this exam
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền thay đổi trạng thái đề thi này.');
        }

        $exam->is_active = !$exam->is_active;
        $exam->save();

        $status = $exam->is_active ? 'kích hoạt' : 'tạm dừng';
        
        return back()->with('success', "Đề thi đã được {$status} thành công!");
    }
}