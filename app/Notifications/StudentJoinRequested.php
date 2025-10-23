<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentJoinRequested extends Notification
{
    use Queueable;

    public function __construct(
        public int $classId,
        public string $className,
        public int $studentId,
        public string $studentName
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'class_join_requested',
            'class_id' => $this->classId,
            'class_name' => $this->className,
            'student_id' => $this->studentId,
            'student_name' => $this->studentName,
            'message' => "Học sinh {$this->studentName} yêu cầu tham gia lớp {$this->className}.",
        ];
    }
}
