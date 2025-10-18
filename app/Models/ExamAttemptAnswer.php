<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_id',
        'answer_text',
        'is_correct',
        'points_earned',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    /**
     * Scopes
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    /**
     * Methods
     */
    public function checkCorrectness()
    {
        $question = $this->question;

        // For multiple choice and true/false
        if (in_array($question->question_type, ['multiple_choice', 'true_false'])) {
            if ($this->answer_id) {
                $selectedAnswer = Answer::find($this->answer_id);
                $this->is_correct = $selectedAnswer && $selectedAnswer->is_correct;
                $this->points_earned = $this->is_correct ? $question->marks : 0;
            }
        }
        // For short answer and essay - manual grading needed
        // For now, set as incorrect, teacher will grade later
        else {
            $this->is_correct = false;
            $this->points_earned = 0;
        }

        $this->save();
        return $this;
    }
}
