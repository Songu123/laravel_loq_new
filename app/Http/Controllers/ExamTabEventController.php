<?php

namespace App\Http\Controllers;

use App\Models\ExamTabEvent;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamTabEventController extends Controller
{
    public function recordEvent(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|integer|exists:exams,id',
            'attempt_id' => 'required|integer|exists:exam_attempts,id', 
            'event_type' => 'required|string|in:tab_switch,window_blur,window_focus,visibility_change,suspicious_key,copy_paste,right_click',
            'event_data' => 'required|array',
            'event_data.action' => 'required|string',
            'event_data.timestamp' => 'required|integer',
            'event_data.question_id' => 'nullable|integer'
        ]);

        $user = Auth::user();
        
        // Kiểm tra attempt có thuộc về user hiện tại không
        $attempt = ExamAttempt::where('id', $request->attempt_id)
                             ->where('user_id', $user->id)
                             ->where('status', 'in_progress')
                             ->first();
        
        if (!$attempt) {
            return response()->json(['error' => 'Invalid attempt'], 403);
        }

        // Lưu sự kiện
        $event = ExamTabEvent::create([
            'user_id' => $user->id,
            'exam_id' => $request->exam_id,
            'attempt_id' => $request->attempt_id,
            'event_type' => $request->event_type,
            'event_data' => $request->event_data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'occurred_at' => Carbon::createFromTimestamp($request->event_data['timestamp'] / 1000)
        ]);

        // Phân tích real-time và cập nhật attempt
        $analysis = $this->analyzeViolationsRealTime($request->attempt_id);
        
        // Cập nhật số liệu vi phạm trong attempt
        $attempt->update([
            'tab_switch_count' => $analysis['tab_switches'],
            'violation_score' => $analysis['violation_score'],
            'is_suspicious' => $analysis['is_suspicious'],
            'needs_review' => $analysis['needs_review']
        ]);

        $response = [
            'success' => true, 
            'event_id' => $event->id,
            'analysis' => $analysis
        ];
        
        // Cảnh báo theo mức độ
        if ($analysis['violation_score'] >= 15) {
            $response['critical_alert'] = 'BÀI THI CÓ THỂ BỊ HỦY - Vi phạm nghiêm trọng!';
            $response['action'] = 'lock_exam';
        } elseif ($analysis['violation_score'] >= 10) {
            $response['warning'] = 'CẢNH BÁO NGHIÊM TRỌNG - Hành vi gian lận được phát hiện!';
            $response['action'] = 'final_warning';
        } elseif ($analysis['tab_switches'] >= 3) {
            $response['warning'] = 'Cảnh báo: Bạn đã chuyển tab ' . $analysis['tab_switches'] . ' lần.';
            $response['action'] = 'warning';
        }

        return response()->json($response);
    }

    public function analyzeViolationsRealTime($attemptId)
    {
        // Đếm các loại vi phạm
        $tabSwitches = ExamTabEvent::where('attempt_id', $attemptId)
                                  ->where('event_type', 'visibility_change')
                                  ->where('event_data->action', 'hidden')
                                  ->count();

        $suspiciousKeys = ExamTabEvent::where('attempt_id', $attemptId)
                                     ->where('event_type', 'suspicious_key')
                                     ->count();

        $copyPasteEvents = ExamTabEvent::where('attempt_id', $attemptId)
                                      ->where('event_type', 'copy_paste')
                                      ->count();

        $rightClickEvents = ExamTabEvent::where('attempt_id', $attemptId)
                                       ->where('event_type', 'right_click')
                                       ->count();

        // Tính điểm vi phạm (weighted scoring)
        $violationScore = ($tabSwitches * 2) + 
                         ($suspiciousKeys * 5) + 
                         ($copyPasteEvents * 3) + 
                         ($rightClickEvents * 1);

        // Phân tích pattern thời gian (các vi phạm liên tiếp trong thời gian ngắn)
        $recentViolations = ExamTabEvent::where('attempt_id', $attemptId)
                                       ->where('occurred_at', '>=', now()->subMinutes(5))
                                       ->whereIn('event_type', ['visibility_change', 'suspicious_key', 'copy_paste'])
                                       ->count();

        if ($recentViolations >= 5) {
            $violationScore += 10; // Penalty cho vi phạm liên tiếp
        }

        return [
            'tab_switches' => $tabSwitches,
            'suspicious_keys' => $suspiciousKeys,
            'copy_paste_events' => $copyPasteEvents,
            'right_click_events' => $rightClickEvents,
            'recent_violations' => $recentViolations,
            'violation_score' => $violationScore,
            'is_suspicious' => $violationScore >= 10,
            'needs_review' => $violationScore >= 6,
            'risk_level' => $this->getRiskLevel($violationScore)
        ];
    }

    private function getRiskLevel($score)
    {
        if ($score >= 15) return 'critical';
        if ($score >= 10) return 'high';
        if ($score >= 6) return 'medium';
        if ($score >= 3) return 'low';
        return 'normal';
    }

    public function getAttemptEvents($attemptId)
    {
        $attempt = ExamAttempt::findOrFail($attemptId);
        
        // Kiểm tra quyền truy cập
        if (Auth::user()->id !== $attempt->user_id && !Auth::user()->can('view-violations')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $events = ExamTabEvent::where('attempt_id', $attemptId)
                              ->orderBy('occurred_at')
                              ->get();
        
        $analysis = $this->analyzeViolationsRealTime($attemptId);
        
        return response()->json([
            'events' => $events,
            'analysis' => $analysis,
            'timeline' => $this->buildViolationTimeline($events)
        ]);
    }

    private function buildViolationTimeline($events)
    {
        return $events->map(function ($event) {
            return [
                'id' => $event->id,
                'time' => $event->occurred_at->format('H:i:s'),
                'timestamp' => $event->occurred_at->timestamp,
                'type' => $event->event_type,
                'description' => $event->event_description,
                'severity' => $event->severity,
                'severity_label' => $event->severity_label,
                'data' => $event->event_data,
                'question_id' => $event->event_data['question_id'] ?? null
            ];
        });
    }
}