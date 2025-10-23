<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JoinRequestRejected extends Notification
{
    use Queueable;

    public function __construct(
        public int $classId,
        public string $className,
        public ?string $note = null
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'class_join_rejected',
            'class_id' => $this->classId,
            'class_name' => $this->className,
            'note' => $this->note,
            'message' => "Yêu cầu tham gia lớp {$this->className} đã bị từ chối." . ($this->note ? " Lý do: {$this->note}" : ''),
        ];
    }
}
