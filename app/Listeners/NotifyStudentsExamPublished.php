<?php

namespace App\Listeners;

use App\Events\ExamPublished;
use App\Models\User;
use App\Notifications\ExamPublishedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyStudentsExamPublished implements ShouldQueue
{
    public function handle(ExamPublished $event): void
    {
        $exam = $event->exam;

        // TODO: thay logic này theo mô hình phân quyền/đối tượng dự thi của bạn
        // Ví dụ: $exam->eligibleStudents() hoặc qua lớp, subject, enrollment pivot,...
        $students = method_exists($exam, 'eligibleStudents')
            ? $exam->eligibleStudents()->get()
            : User::query()->where('role', 'student')->get();

        foreach ($students as $student) {
            $student->notify(new ExamPublishedNotification(
                examId: $exam->id,
                examTitle: $exam->title ?? ('Exam #'.$exam->id),
                startAt: optional($exam->start_at)->toISOString(),
                endAt: optional($exam->end_at)->toISOString(),
            ));
        }
    }
}