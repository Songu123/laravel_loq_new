<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExamPublishedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $examId,
        public string $examTitle,
        public ?string $startAt, // ISO string
        public ?string $endAt
    ) {}

    public function via($notifiable): array
    {
        return ['database']; // có thể thêm 'mail', 'broadcast'
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'exam_published',
            'exam_id' => $this->examId,
            'title' => $this->examTitle,
            'start_at' => $this->startAt,
            'end_at' => $this->endAt,
            'message' => "Đề thi '{$this->examTitle}' đã sẵn sàng.",
        ];
    }
}