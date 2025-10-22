<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use App\Models\ExamTabEvent;
use App\Models\ExamAnswer;
use App\Models\ExamViolation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ViolationAnalysisController extends Controller
{
    /**
     * Log a violation during exam
     */
    public function logViolation(Request $request)
    {
        $validated = $request->validate([
            'attempt_id' => 'nullable|exists:exam_attempts,id',
            'exam_id' => 'required|exists:exams,id',
            'violation_type' => 'required|in:tab_switch,copy_paste,fullscreen_exit,mouse_leave,right_click,keyboard_shortcut,time_anomaly,multiple_devices,suspicious_pattern',
            'description' => 'required|string|max:500',
            'severity' => 'required|integer|min:1|max:4',
            'metadata' => 'nullable|array'
        ]);

        $violation = ExamViolation::create([
            'attempt_id' => $validated['attempt_id'],
            'user_id' => Auth::id(),
            'exam_id' => $validated['exam_id'],
            'violation_type' => $validated['violation_type'],
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'metadata' => $validated['metadata'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'violated_at' => now()
        ]);

        // Check if should auto-flag attempt
        if ($validated['attempt_id']) {
            $this->checkAutoFlag($validated['attempt_id']);
        }

        return response()->json([
            'success' => true,
            'violation_id' => $violation->id,
            'total_violations' => $this->getTotalViolations($validated['attempt_id'] ?? null, Auth::id(), $validated['exam_id'])
        ]);
    }

    /**
     * Get violations for an attempt
     */
    public function getAttemptViolations($attemptId)
    {
        $attempt = ExamAttempt::findOrFail($attemptId);
        
        // Check authorization (teacher or owner)
        if (Auth::user()->role !== 'teacher' && $attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $violations = ExamViolation::where('attempt_id', $attemptId)
            ->orderBy('violated_at', 'desc')
            ->get();

        return response()->json([
            'attempt' => $attempt,
            'violations' => $violations,
            'statistics' => [
                'total' => $violations->count(),
                'by_severity' => $violations->groupBy('severity')->map->count(),
                'by_type' => $violations->groupBy('violation_type')->map->count(),
                'high_severity' => $violations->where('severity', '>=', 3)->count()
            ]
        ]);
    }

    /**
     * Check if attempt should be auto-flagged
     */
    private function checkAutoFlag($attemptId)
    {
        $attempt = ExamAttempt::find($attemptId);
        if (!$attempt) return;

        $violations = ExamViolation::where('attempt_id', $attemptId)->get();

        // Auto-flag conditions:
        $shouldFlag = $violations->count() >= 5 ||
                     $violations->where('severity', 4)->count() >= 2 ||
                     $violations->where('severity', '>=', 3)->count() >= 3;

        if ($shouldFlag && !isset($attempt->is_flagged)) {
            $attempt->update(['is_flagged' => true]);
        }
    }

    /**
     * Get total violations count
     */
    private function getTotalViolations($attemptId, $userId, $examId)
    {
        if ($attemptId) {
            return ExamViolation::where('attempt_id', $attemptId)->count();
        } else {
            return ExamViolation::where('user_id', $userId)
                ->where('exam_id', $examId)
                ->count();
        }
    }

    public function analyzeAttempt($attemptId)
    {
        $attempt = ExamAttempt::with(['exam', 'user', 'answers', 'tabEvents'])
                              ->findOrFail($attemptId);

        $analysis = [
            'basic_info' => $this->getBasicInfo($attempt),
            'time_analysis' => $this->analyzeTimePatterns($attempt),
            'behavior_analysis' => $this->analyzeBehaviorPatterns($attempt),
            'answer_analysis' => $this->analyzeAnswerPatterns($attempt),
            'similarity_analysis' => $this->analyzeSimilarity($attempt),
            'risk_assessment' => $this->assessRisk($attempt),
            'recommendations' => $this->getRecommendations($attempt)
        ];

        return response()->json($analysis);
    }

    private function getBasicInfo($attempt)
    {
        $events = $attempt->tabEvents;
        
        return [
            'attempt_id' => $attempt->id,
            'user_name' => $attempt->user->name,
            'exam_title' => $attempt->exam->title,
            'duration' => $attempt->time_spent,
            'score' => $attempt->score,
            'total_events' => $events->count(),
            'tab_switches' => $events->where('event_type', 'visibility_change')
                                   ->where('event_data.action', 'hidden')->count(),
            'suspicious_keys' => $events->where('event_type', 'suspicious_key')->count(),
        ];
    }

    private function analyzeTimePatterns($attempt)
    {
        $answers = $attempt->answers()->whereNotNull('answered_at')->get();
        
        if ($answers->isEmpty()) {
            return ['warning' => 'Không có dữ liệu thời gian trả lời'];
        }

        $timings = $answers->pluck('time_spent')->filter(function($time) {
            return $time > 0;
        });

        $avgTime = $timings->avg();
        $minTime = $timings->min();
        $maxTime = $timings->max();
        $stdDev = $this->calculateStandardDeviation($timings->toArray());

        // Phát hiện câu trả lời quá nhanh (< 5 giây)
        $tooFastAnswers = $answers->where('time_spent', '<', 5)->count();
        
        // Phát hiện câu trả lời có thời gian bất thường
        $unusualTimings = $answers->filter(function($answer) use ($avgTime, $stdDev) {
            return abs($answer->time_spent - $avgTime) > (2 * $stdDev);
        })->count();

        return [
            'average_time' => round($avgTime, 2),
            'min_time' => $minTime,
            'max_time' => $maxTime,
            'standard_deviation' => round($stdDev, 2),
            'too_fast_answers' => $tooFastAnswers,
            'unusual_timings' => $unusualTimings,
            'time_consistency_score' => $this->calculateTimeConsistencyScore($timings->toArray()),
            'flags' => $this->getTimingFlags($tooFastAnswers, $unusualTimings, $avgTime)
        ];
    }

    private function analyzeBehaviorPatterns($attempt)
    {
        $events = $attempt->tabEvents()->orderBy('occurred_at')->get();
        
        // Phân tích pattern chuyển tab
        $tabSwitchPattern = $this->analyzeTabSwitchPattern($events);
        
        // Phân tích thời gian giữa các vi phạm
        $violationIntervals = $this->analyzeViolationIntervals($events);
        
        // Phân tích sự kiện theo câu hỏi
        $eventsByQuestion = $this->analyzeEventsByQuestion($events);

        return [
            'tab_switch_pattern' => $tabSwitchPattern,
            'violation_intervals' => $violationIntervals,
            'events_by_question' => $eventsByQuestion,
            'behavior_score' => $this->calculateBehaviorScore($events),
            'risk_indicators' => $this->identifyRiskIndicators($events)
        ];
    }

    private function analyzeAnswerPatterns($attempt)
    {
        $answers = $attempt->answers()->with('question')->get();
        
        // Phân tích độ chính xác theo thời gian
        $accuracyOverTime = $this->analyzeAccuracyOverTime($answers);
        
        // Phân tích pattern thay đổi đáp án
        $answerChanges = $this->analyzeAnswerChanges($answers);
        
        // Phân tích streak (chuỗi đúng/sai)
        $streakAnalysis = $this->analyzeAnswerStreaks($answers);

        return [
            'total_answers' => $answers->count(),
            'correct_answers' => $answers->where('is_correct', true)->count(),
            'accuracy_rate' => $answers->count() > 0 ? 
                ($answers->where('is_correct', true)->count() / $answers->count()) * 100 : 0,
            'accuracy_over_time' => $accuracyOverTime,
            'answer_changes' => $answerChanges,
            'streak_analysis' => $streakAnalysis,
            'suspicious_patterns' => $this->identifySuspiciousAnswerPatterns($answers)
        ];
    }

    private function analyzeSimilarity($attempt)
    {
        // So sánh với các attempt khác trong cùng kỳ thi
        $similarAttempts = ExamAttempt::where('exam_id', $attempt->exam_id)
                                     ->where('id', '!=', $attempt->id)
                                     ->where('status', 'submitted')
                                     ->get();

        $similarities = [];
        
        foreach ($similarAttempts as $otherAttempt) {
            $similarity = $this->calculateSimilarity($attempt, $otherAttempt);
            if ($similarity['score'] > 0.7) { // Ngưỡng nghi vấn
                $similarities[] = [
                    'attempt_id' => $otherAttempt->id,
                    'user_name' => $otherAttempt->user->name,
                    'similarity_score' => $similarity['score'],
                    'matching_answers' => $similarity['matching_answers'],
                    'total_compared' => $similarity['total_compared']
                ];
            }
        }

        return [
            'similar_attempts' => $similarities,
            'highest_similarity' => $similarities ? max(array_column($similarities, 'similarity_score')) : 0,
            'is_suspicious' => !empty($similarities)
        ];
    }

    private function calculateSimilarity($attempt1, $attempt2)
    {
        $answers1 = $attempt1->answers()->get()->keyBy('question_id');
        $answers2 = $attempt2->answers()->get()->keyBy('question_id');
        
        $commonQuestions = $answers1->keys()->intersect($answers2->keys());
        $matchingAnswers = 0;
        
        foreach ($commonQuestions as $questionId) {
            $choices1 = $answers1[$questionId]->selected_choices ?? [];
            $choices2 = $answers2[$questionId]->selected_choices ?? [];
            
            sort($choices1);
            sort($choices2);
            
            if ($choices1 === $choices2) {
                $matchingAnswers++;
            }
        }
        
        $totalCompared = $commonQuestions->count();
        $score = $totalCompared > 0 ? $matchingAnswers / $totalCompared : 0;
        
        return [
            'score' => $score,
            'matching_answers' => $matchingAnswers,
            'total_compared' => $totalCompared
        ];
    }

    private function assessRisk($attempt)
    {
        $riskFactors = [];
        $riskScore = 0;
        
        // Factor 1: Tab switches
        $tabSwitches = $attempt->tab_switch_count;
        if ($tabSwitches >= 5) {
            $riskFactors[] = "Chuyển tab quá nhiều ($tabSwitches lần)";
            $riskScore += 30;
        } elseif ($tabSwitches >= 3) {
            $riskFactors[] = "Chuyển tab nhiều ($tabSwitches lần)";
            $riskScore += 15;
        }
        
        // Factor 2: Violation score
        if ($attempt->violation_score >= 15) {
            $riskFactors[] = "Điểm vi phạm cao ({$attempt->violation_score})";
            $riskScore += 40;
        } elseif ($attempt->violation_score >= 10) {
            $riskFactors[] = "Điểm vi phạm trung bình ({$attempt->violation_score})";
            $riskScore += 20;
        }
        
        // Factor 3: Time patterns
        $avgTime = $attempt->answers()->avg('time_spent');
        if ($avgTime < 10) {
            $riskFactors[] = "Thời gian trả lời quá nhanh (TB: {$avgTime}s)";
            $riskScore += 25;
        }
        
        // Factor 4: Accuracy vs time
        $accuracy = $attempt->answers()->where('is_correct', true)->count() / 
                   max(1, $attempt->answers()->count());
        if ($accuracy > 0.9 && $avgTime < 15) {
            $riskFactors[] = "Độ chính xác cao bất thường với thời gian ngắn";
            $riskScore += 35;
        }

        return [
            'risk_score' => $riskScore,
            'risk_level' => $this->getRiskLevelFromScore($riskScore),
            'risk_factors' => $riskFactors,
            'requires_manual_review' => $riskScore >= 50,
            'recommended_action' => $this->getRecommendedAction($riskScore)
        ];
    }

    private function getRecommendations($attempt)
    {
        $recommendations = [];
        
        if ($attempt->is_suspicious) {
            $recommendations[] = "Yêu cầu review thủ công bởi giảng viên";
        }
        
        if ($attempt->tab_switch_count >= 5) {
            $recommendations[] = "Xem xét hủy kết quả do vi phạm nghiêm trọng";
        }
        
        if ($attempt->violation_score >= 15) {
            $recommendations[] = "Cấm học sinh tham gia các kỳ thi tiếp theo";
        }
        
        // Thêm các recommendation khác dựa trên phân tích...
        
        return $recommendations;
    }

    // Helper methods
    private function calculateStandardDeviation($array)
    {
        $count = count($array);
        if ($count <= 1) return 0;
        
        $mean = array_sum($array) / $count;
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $array)) / ($count - 1);
        
        return sqrt($variance);
    }

    private function calculateTimeConsistencyScore($timings)
    {
        if (count($timings) <= 1) return 100;
        
        $stdDev = $this->calculateStandardDeviation($timings);
        $mean = array_sum($timings) / count($timings);
        
        $cv = $mean > 0 ? ($stdDev / $mean) : 0; // Coefficient of variation
        
        // Score từ 0-100, càng thấp càng consistent
        return max(0, 100 - ($cv * 100));
    }

    private function getRiskLevelFromScore($score)
    {
        if ($score >= 70) return 'critical';
        if ($score >= 50) return 'high';  
        if ($score >= 30) return 'medium';
        if ($score >= 15) return 'low';
        return 'normal';
    }

    private function getRecommendedAction($score)
    {
        if ($score >= 70) return 'Hủy kết quả và cấm thi';
        if ($score >= 50) return 'Review thủ công bắt buộc';
        if ($score >= 30) return 'Cảnh báo và theo dõi';
        if ($score >= 15) return 'Ghi nhận vi phạm nhẹ';
        return 'Không có hành động';
    }

    // Thêm các helper methods khác...
    private function analyzeTabSwitchPattern($events) { /* Implementation */ }
    private function analyzeViolationIntervals($events) { /* Implementation */ }
    private function analyzeEventsByQuestion($events) { /* Implementation */ }
    private function calculateBehaviorScore($events) { /* Implementation */ }
    private function identifyRiskIndicators($events) { /* Implementation */ }
    private function analyzeAccuracyOverTime($answers) { /* Implementation */ }
    private function analyzeAnswerChanges($answers) { /* Implementation */ }
    private function analyzeAnswerStreaks($answers) { /* Implementation */ }
    private function identifySuspiciousAnswerPatterns($answers) { /* Implementation */ }
    private function getTimingFlags($tooFast, $unusual, $avg) { /* Implementation */ }
}