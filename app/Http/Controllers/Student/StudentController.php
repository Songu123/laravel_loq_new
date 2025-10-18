<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display student dashboard
     */
    public function dashboard()
    {
        $userId = auth()->id();

        // Statistics
        $availableExams = Exam::active()->public()->count();
        $completedExams = ExamAttempt::forUser($userId)->completed()->count();
        $totalExams = Exam::active()->public()->count();
        
        $averageScore = ExamAttempt::forUser($userId)->completed()->avg('percentage') ?? 0;
        $highestScore = ExamAttempt::forUser($userId)->completed()->max('percentage') ?? 0;
        $passedExams = ExamAttempt::forUser($userId)->passed()->count();
        
        // Recent exams
        $recentExams = Exam::active()
            ->public()
            ->with(['category', 'creator'])
            ->withCount('questions')
            ->latest()
            ->take(5)
            ->get();

        // Recent results
        $recentResults = ExamAttempt::forUser($userId)
            ->with('exam.category')
            ->completed()
            ->latest()
            ->take(5)
            ->get();

        // Categories with exam count
        $categories = Category::withCount(['exams' => function($query) {
            $query->active()->public();
        }])->get();

        // Ranking (optional - simplified)
        $ranking = ExamAttempt::select('user_id', DB::raw('AVG(percentage) as avg_score'))
            ->groupBy('user_id')
            ->orderByDesc('avg_score')
            ->pluck('user_id')
            ->search($userId);
        
        $ranking = $ranking !== false ? $ranking + 1 : null;

        return view('student.dashboard', compact(
            'availableExams',
            'completedExams',
            'totalExams',
            'averageScore',
            'highestScore',
            'passedExams',
            'recentExams',
            'recentResults',
            'categories',
            'ranking'
        ));
    }

    /**
     * Display all available exams
     */
    public function exams(Request $request)
    {
        $query = Exam::active()
            ->public()
            ->with(['category', 'creator'])
            ->withCount('questions');

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'popular':
                $query->withCount('attempts')->orderByDesc('attempts_count');
                break;
            case 'easiest':
                $query->orderBy('difficulty_level', 'asc');
                break;
            case 'hardest':
                $query->orderBy('difficulty_level', 'desc');
                break;
            default: // newest
                $query->latest();
        }

        $exams = $query->paginate(12)->withQueryString();

        // Add user's attempt info to each exam
        $userId = auth()->id();
        $exams->each(function($exam) use ($userId) {
            $exam->my_attempt = ExamAttempt::where('exam_id', $exam->id)
                ->where('user_id', $userId)
                ->latest()
                ->first();
            
            $exam->attempts_count = ExamAttempt::where('exam_id', $exam->id)->count();
            $exam->is_new = $exam->created_at->isAfter(now()->subDays(7));
            $exam->canTake = $this->canTakeExam($exam);
        });

        $categories = Category::all();

        return view('student.exams.index', compact('exams', 'categories'));
    }

    /**
     * Show exam details
     */
    public function show(Exam $exam)
    {
        $exam->load(['category', 'creator', 'questions']);

        // Get user's previous attempts
        $myAttempts = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $canTake = $this->canTakeExam($exam);

        $exam->attempts_count = ExamAttempt::where('exam_id', $exam->id)->count();

        return view('student.exams.show', compact('exam', 'myAttempts', 'canTake'));
    }

    /**
     * Start taking an exam
     */
    public function take(Exam $exam)
    {
        // Check if can take
        if (!$this->canTakeExam($exam)) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Bạn không thể thi đề thi này lúc này.');
        }

        // Load questions with answers
        $questions = $exam->questions()->with('answers')->get();

        // Randomize if needed
        if ($exam->randomize_questions) {
            $questions = $questions->shuffle();
        }

        return view('student.exams.take', compact('exam', 'questions'));
    }

    /**
     * Submit exam answers
     */
    public function submit(Request $request, Exam $exam)
    {
        DB::beginTransaction();
        
        try {
            // Get all questions first
            $questions = $exam->questions()->with('answers')->get();
            
            // Create attempt
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => auth()->id(),
                'total_questions' => $questions->count(), // Use actual count
                'started_at' => now()->subSeconds($request->time_spent ?? 0),
                'completed_at' => now(),
                'time_spent' => $request->time_spent ?? 0,
            ]);

            // Process each answer
            foreach ($questions as $question) {
                $userAnswer = $request->input('answers.' . $question->id);

                $attemptAnswer = ExamAttemptAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'answer_id' => in_array($question->question_type, ['multiple_choice', 'true_false']) 
                        ? $userAnswer 
                        : null,
                    'answer_text' => in_array($question->question_type, ['short_answer', 'essay']) 
                        ? $userAnswer 
                        : null,
                ]);

                // Check correctness
                $attemptAnswer->checkCorrectness();
            }

            // Calculate final score
            $attempt->calculateScore();

            DB::commit();

            // Flash detailed result info
            $resultMessage = sprintf(
                'Bài thi đã được nộp thành công! Bạn đạt %.1f%% (%d/%d câu đúng)',
                $attempt->percentage,
                $attempt->correct_answers,
                $attempt->total_questions
            );

            return redirect()->route('student.results.show', $attempt)
                ->with('success', $resultMessage)
                ->with('attempt_id', $attempt->id)
                ->with('show_confetti', $attempt->percentage >= 80);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Có lỗi xảy ra khi nộp bài: ' . $e->getMessage());
        }
    }

    /**
     * Show exam history
     */
    public function history(Request $request)
    {
        $userId = auth()->id();

        $query = ExamAttempt::forUser($userId)
            ->with(['exam.category'])
            ->completed();

        // Search
        if ($request->filled('search')) {
            $query->whereHas('exam', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('exam', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // Filter by result
        if ($request->filled('result')) {
            if ($request->result == 'passed') {
                $query->passed();
            } elseif ($request->result == 'failed') {
                $query->failed();
            }
        }

        $attempts = $query->latest()->paginate(15)->withQueryString();

        // Statistics
        $totalAttempts = ExamAttempt::forUser($userId)->completed()->count();
        $passedAttempts = ExamAttempt::forUser($userId)->passed()->count();
        $averageScore = ExamAttempt::forUser($userId)->completed()->avg('percentage') ?? 0;
        $highestScore = ExamAttempt::forUser($userId)->completed()->max('percentage') ?? 0;

        $categories = Category::all();

        return view('student.history', compact(
            'attempts',
            'totalAttempts',
            'passedAttempts',
            'averageScore',
            'highestScore',
            'categories'
        ));
    }

    /**
     * Show exam result details
     */
    public function resultDetail(ExamAttempt $attempt)
    {
        // Authorize
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem kết quả này.');
        }

        $attempt->load([
            'exam.category',
            'exam.creator',
            'answers.question.answers',
            'answers.answer'
        ]);

        return view('student.results.show', compact('attempt'));
    }

    /**
     * Check if user can take the exam
     */
    private function canTakeExam(Exam $exam): bool
    {
        // Check if active and public
        if (!$exam->is_active || !$exam->is_public) {
            return false;
        }

        // Check start time
        if ($exam->start_time && $exam->start_time->isFuture()) {
            return false;
        }

        // Check end time
        if ($exam->end_time && $exam->end_time->isPast()) {
            return false;
        }

        return true;
    }
}
