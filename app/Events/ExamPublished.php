<?php

namespace App\Events;

use App\Models\Exam;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamPublished
{
    use Dispatchable, SerializesModels;

    public function __construct(public Exam $exam) {}
    protected $listen = [
        \App\Events\ExamPublished::class => [
            \App\Listeners\NotifyStudentsExamPublished::class,
        ],
    ];
}
