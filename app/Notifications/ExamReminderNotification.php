<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExamReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $examId,
        public string $examTitle,
        public string $startAt,
        public int $minutesBefore
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'exam_reminder',
            'exam_id' => $this->examId,
            'title' => $this->examTitle,
            'start_at' => $this->startAt,
            'minutes_before' => $this->minutesBefore,
            'message' => "Sắp đến giờ thi '{$this->examTitle}' trong {$this->minutesBefore} phút.",
        ];
    }
}