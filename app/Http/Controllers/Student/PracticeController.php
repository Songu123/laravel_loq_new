<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\Question;
use App\Models\Exam;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PracticeController extends Controller
{
    /**
     * Show practice dashboard with statistics
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get all attempts with answers (completed attempts only)
        $attempts = ExamAttempt::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->with(['exam.category', 'answers.question'])
            ->get();
        
        // Calculate wrong answers
        $wrongAnswers = ExamAnswer::whereHas('attempt', function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->whereNotNull('completed_at');
        })
        ->where('is_correct', false)
        ->with(['question', 'attempt.exam.category'])
        ->get();
        
        // Statistics
        $stats = [
            'total_attempts' => $attempts->count(),
            'total_questions' => ExamAnswer::whereHas('attempt', function($query) use ($userId) {
                $query->where('user_id', $userId)->whereNotNull('completed_at');
            })->count(),
            'correct_answers' => ExamAnswer::whereHas('attempt', function($query) use ($userId) {
                $query->where('user_id', $userId)->whereNotNull('completed_at');
            })->where('is_correct', true)->count(),
            'wrong_answers' => $wrongAnswers->count(),
            'accuracy_rate' => 0,
        ];
        
        if ($stats['total_questions'] > 0) {
            $stats['accuracy_rate'] = round(($stats['correct_answers'] / $stats['total_questions']) * 100, 1);
        }
        
        // Category performance
        $categoryPerformance = $this->getCategoryPerformance($userId);
        
        // Question type performance
        $questionTypePerformance = $this->getQuestionTypePerformance($userId);
        
        // Difficulty analysis
        $difficultyAnalysis = $this->getDifficultyAnalysis($userId);
        
        // Recent practice sessions
        $recentPractice = ExamAttempt::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->with('exam')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Improvement areas
        $improvementAreas = $this->getImprovementAreas($categoryPerformance, $questionTypePerformance);
        
        // Weekly progress
        $weeklyProgress = $this->getWeeklyProgress($userId);
        
        return view('student.practice.index', compact(
            'stats',
            'wrongAnswers',
            'categoryPerformance',
            'questionTypePerformance',
            'difficultyAnalysis',
            'recentPractice',
            'improvementAreas',
            'weeklyProgress'
        ));
    }
    
    /**
     * Show wrong answers for practice
     */
    public function wrongAnswers(Request $request)
    {
        $userId = Auth::id();
        $categoryId = $request->get('category');
        $questionType = $request->get('type');
        
        $query = ExamAnswer::whereHas('attempt', function($q) use ($userId) {
            $q->where('user_id', $userId)->whereNotNull('completed_at');
        })
        ->where('is_correct', false)
        ->with(['question.answers', 'answer', 'attempt.exam.category']);
        
        if ($categoryId) {
            $query->whereHas('attempt.exam', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        
        if ($questionType) {
            $query->whereHas('question', function($q) use ($questionType) {
                $q->where('question_type', $questionType);
            });
        }
        
        $wrongAnswers = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = Category::all();
        
        return view('student.practice.wrong-answers', compact('wrongAnswers', 'categories'));
    }
    
    /**
     * Start practice session
     */
    public function startPractice(Request $request)
    {
        $userId = Auth::id();
        $mode = $request->get('mode', 'wrong'); // wrong, category, random
        $categoryId = $request->get('category_id');
        $limit = $request->get('limit', 10);
        
        $questions = collect();
        
        switch ($mode) {
            case 'wrong':
                // Get wrong answer questions
                $wrongQuestionIds = ExamAnswer::whereHas('attempt', function($q) use ($userId) {
                    $q->where('user_id', $userId)->whereNotNull('completed_at');
                })
                ->where('is_correct', false)
                ->pluck('question_id')
                ->unique();
                
                $questions = Question::whereIn('id', $wrongQuestionIds)
                    ->with('answers')
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get();
                break;
                
            case 'category':
                if ($categoryId) {
                    $questions = Question::whereHas('exam', function($q) use ($categoryId) {
                        $q->where('category_id', $categoryId);
                    })
                    ->with('answers')
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get();
                }
                break;
                
            case 'random':
                $questions = Question::with('answers')
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get();
                break;
        }
        
        if ($questions->isEmpty()) {
            return redirect()->route('student.practice.index')
                ->with('error', 'Không tìm thấy câu hỏi phù hợp để luyện tập.');
        }
        
        // Store practice session in session
        session([
            'practice_session' => [
                'questions' => $questions->pluck('id')->toArray(),
                'mode' => $mode,
                'category_id' => $categoryId,
                'started_at' => now(),
            ]
        ]);
        
        $startTime = now()->timestamp;
        
        return view('student.practice.session', compact('questions', 'mode', 'startTime'));
    }
    
    /**
     * Submit practice session
     */
    public function submitPractice(Request $request)
    {
        $userId = Auth::id();
        $answers = $request->input('answers', []);
        $startTime = $request->input('start_time');
        $practiceSession = session('practice_session');
        
        if (!$practiceSession) {
            return redirect()->route('student.practice.index')
                ->with('error', 'Phiên luyện tập không hợp lệ.');
        }
        
        $results = [
            'details' => [],
            'total' => 0,
            'correct' => 0,
            'wrong' => 0,
            'accuracy' => 0,
            'time_spent' => $startTime ? (now()->timestamp - $startTime) : 0,
        ];
        
        $wrongQuestionIds = [];
        
        foreach ($answers as $questionId => $userAnswer) {
            $question = Question::with('answers')->find($questionId);
            if (!$question) continue;
            
            $results['total']++;
            
            // Check if answer is correct
            $isCorrect = false;
            $correctAnswer = '';
            $userAnswerText = '';
            
            if (in_array($question->question_type, ['multiple_choice', 'true_false'])) {
                // For multiple choice - userAnswer is answer_id
                $selectedAnswer = $question->answers->firstWhere('id', $userAnswer);
                $correctAnswerObj = $question->answers->firstWhere('is_correct', true);
                
                $userAnswerText = $selectedAnswer ? $selectedAnswer->answer_text : 'Không trả lời';
                $correctAnswer = $correctAnswerObj ? $correctAnswerObj->answer_text : 'N/A';
                $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
            } else {
                // For text-based answers
                $correctAnswerObj = $question->answers->firstWhere('is_correct', true);
                $correctAnswer = $correctAnswerObj ? $correctAnswerObj->answer_text : '';
                $userAnswerText = $userAnswer;
                
                // Compare text (case-insensitive, trim whitespace)
                $isCorrect = (strtolower(trim($userAnswer)) == strtolower(trim($correctAnswer)));
            }
            
            if ($isCorrect) {
                $results['correct']++;
            } else {
                $results['wrong']++;
                $wrongQuestionIds[] = $questionId;
            }
            
            $results['details'][] = [
                'question_id' => $questionId,
                'question' => $question->question_text,
                'user_answer' => $userAnswerText ?: 'Không trả lời',
                'correct_answer' => $correctAnswer,
                'is_correct' => $isCorrect,
                'explanation' => $question->explanation,
            ];
        }
        
        if ($results['total'] > 0) {
            $results['accuracy'] = round(($results['correct'] / $results['total']) * 100, 1);
        }
        
        // Clear session
        session()->forget('practice_session');
        
        return view('student.practice.results', compact('results', 'wrongQuestionIds'));
    }
    
    /**
     * Get category performance
     */
    private function getCategoryPerformance($userId)
    {
        $performance = [];
        
        $categoryStats = ExamAnswer::whereHas('attempt', function($query) use ($userId) {
            $query->where('user_id', $userId)->whereNotNull('completed_at');
        })
        ->with('attempt.exam.category')
        ->get()
        ->groupBy(function($answer) {
            return $answer->attempt->exam->category->name ?? 'Khác';
        })
        ->map(function($answers, $categoryName) {
            $total = $answers->count();
            $correct = $answers->where('is_correct', true)->count();
            $accuracy = $total > 0 ? round(($correct / $total) * 100, 1) : 0;
            
            return [
                'name' => $categoryName,
                'total' => $total,
                'correct' => $correct,
                'wrong' => $total - $correct,
                'accuracy' => $accuracy,
            ];
        })
        ->sortByDesc('total')
        ->values();
        
        return $categoryStats;
    }
    
    /**
     * Get question type performance
     */
    private function getQuestionTypePerformance($userId)
    {
        $performance = ExamAnswer::whereHas('attempt', function($query) use ($userId) {
            $query->where('user_id', $userId)->whereNotNull('completed_at');
        })
        ->with('question')
        ->get()
        ->groupBy(function($answer) {
            return $answer->question->question_type ?? 'unknown';
        })
        ->map(function($answers, $type) {
            $total = $answers->count();
            $correct = $answers->where('is_correct', true)->count();
            $accuracy = $total > 0 ? round(($correct / $total) * 100, 1) : 0;
            
            $typeNames = [
                'multiple_choice' => 'Trắc nghiệm',
                'true_false' => 'Đúng/Sai',
                'short_answer' => 'Câu ngắn',
                'essay' => 'Tự luận',
            ];
            
            return [
                'type' => $type,
                'name' => $typeNames[$type] ?? $type,
                'total' => $total,
                'correct' => $correct,
                'wrong' => $total - $correct,
                'accuracy' => $accuracy,
            ];
        })
        ->sortByDesc('total')
        ->values();
        
        return $performance;
    }
    
    /**
     * Get difficulty analysis
     */
    private function getDifficultyAnalysis($userId)
    {
        $analysis = ExamAnswer::whereHas('attempt', function($query) use ($userId) {
            $query->where('user_id', $userId)->whereNotNull('completed_at');
        })
        ->with('attempt.exam')
        ->get()
        ->groupBy(function($answer) {
            return $answer->attempt->exam->difficulty_level ?? 'medium';
        })
        ->map(function($answers, $difficulty) {
            $total = $answers->count();
            $correct = $answers->where('is_correct', true)->count();
            $accuracy = $total > 0 ? round(($correct / $total) * 100, 1) : 0;
            
            $difficultyNames = [
                'easy' => 'Dễ',
                'medium' => 'Trung bình',
                'hard' => 'Khó',
            ];
            
            return [
                'level' => $difficulty,
                'name' => $difficultyNames[$difficulty] ?? $difficulty,
                'total' => $total,
                'correct' => $correct,
                'wrong' => $total - $correct,
                'accuracy' => $accuracy,
            ];
        })
        ->sortBy(function($item) {
            $order = ['easy' => 1, 'medium' => 2, 'hard' => 3];
            return $order[$item['level']] ?? 4;
        })
        ->values();
        
        return $analysis;
    }
    
    /**
     * Get improvement areas
     */
    private function getImprovementAreas($categoryPerformance, $questionTypePerformance)
    {
        $areas = [];
        
        // Check category performance
        foreach ($categoryPerformance as $category) {
            if ($category['accuracy'] < 70 && $category['total'] >= 3) {
                $areas[] = [
                    'type' => 'category',
                    'name' => $category['name'],
                    'accuracy' => $category['accuracy'],
                    'priority' => $category['accuracy'] < 50 ? 'high' : 'medium',
                    'suggestion' => "Bạn cần cải thiện kiến thức về {$category['name']}. Độ chính xác hiện tại: {$category['accuracy']}%",
                ];
            }
        }
        
        // Check question type performance
        foreach ($questionTypePerformance as $type) {
            if ($type['accuracy'] < 70 && $type['total'] >= 3) {
                $areas[] = [
                    'type' => 'question_type',
                    'name' => $type['name'],
                    'accuracy' => $type['accuracy'],
                    'priority' => $type['accuracy'] < 50 ? 'high' : 'medium',
                    'suggestion' => "Bạn cần luyện tập thêm dạng câu hỏi {$type['name']}. Độ chính xác hiện tại: {$type['accuracy']}%",
                ];
            }
        }
        
        // Sort by priority and accuracy
        usort($areas, function($a, $b) {
            if ($a['priority'] === 'high' && $b['priority'] !== 'high') return -1;
            if ($a['priority'] !== 'high' && $b['priority'] === 'high') return 1;
            return $a['accuracy'] <=> $b['accuracy'];
        });
        
        return array_slice($areas, 0, 5); // Top 5 areas
    }
    
    /**
     * Get weekly progress
     */
    private function getWeeklyProgress($userId)
    {
        $progress = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            
            $dayAttempts = ExamAttempt::where('user_id', $userId)
                ->whereNotNull('completed_at')
                ->whereDate('created_at', $dateStr)
                ->with('answers')
                ->get();
            
            $totalQuestions = 0;
            $correctAnswers = 0;
            
            foreach ($dayAttempts as $attempt) {
                $totalQuestions += $attempt->answers->count();
                $correctAnswers += $attempt->answers->where('is_correct', true)->count();
            }
            
            $accuracy = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0;
            
            $progress[] = [
                'date' => $date->format('d/m'),
                'day' => $date->format('D'),
                'attempts' => $dayAttempts->count(),
                'questions' => $totalQuestions,
                'accuracy' => $accuracy,
            ];
        }
        
        return $progress;
    }
}
