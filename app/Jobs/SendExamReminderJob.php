<?php

namespace App\Jobs;

use App\Models\Exam;
use App\Models\User;
use App\Notifications\ExamReminderNotification;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendExamReminderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $minutesBefore = 15) {}

    public function handle(): void
    {
        $now = CarbonImmutable::now();
        $windowStart = $now->addMinutes($this->minutesBefore);
        $windowEnd = $windowStart->addMinute(); // cửa sổ 1 phút

        $exams = Exam::query()
            ->where('is_published', true)
            ->whereNotNull('start_at')
            ->whereBetween('start_at', [$windowStart, $windowEnd])
            ->get();

        foreach ($exams as $exam) {
            $students = method_exists($exam, 'eligibleStudents')
                ? $exam->eligibleStudents()->get()
                : User::query()->where('role', 'student')->get();

            foreach ($students as $student) {
                $student->notify(new ExamReminderNotification(
                    examId: $exam->id,
                    examTitle: $exam->title ?? ('Exam #'.$exam->id),
                    startAt: $exam->start_at->toISOString(),
                    minutesBefore: $this->minutesBefore
                ));
            }
        }
    }
}