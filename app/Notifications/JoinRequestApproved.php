<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JoinRequestApproved extends Notification
{
    use Queueable;

    public function __construct(
        public int $classId,
        public string $className
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'class_join_approved',
            'class_id' => $this->classId,
            'class_name' => $this->className,
            'message' => "Yêu cầu tham gia lớp {$this->className} đã được chấp thuận.",
        ];
    }
}
