<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExamCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $attemptId,
        public int $examId,
        public string $examTitle,
        public int $correctAnswers,
        public int $totalQuestions,
        public float $percentage,
    ) {}

    public function via($notifiable): array
    {
        return ['database']; // có thể mở rộng thêm 'mail', 'broadcast' khi cần
    }

    public function toArray($notifiable): array
    {
        $passed = $this->percentage >= 50;

        return [
            'type' => 'exam_completed',
            'attempt_id' => $this->attemptId,
            'exam_id' => $this->examId,
            'title' => $this->examTitle,
            'correct_answers' => $this->correctAnswers,
            'total_questions' => $this->totalQuestions,
            'percentage' => round($this->percentage, 1),
            'passed' => $passed,
            'message' => $passed
                ? "Chúc mừng! Bạn đạt {$this->correctAnswers}/{$this->totalQuestions} câu đúng ({$this->percentage}%)."
                : "Bạn hoàn thành đề '{$this->examTitle}'. Kết quả: {$this->correctAnswers}/{$this->totalQuestions} ({$this->percentage}%).",
        ];
    }
}
