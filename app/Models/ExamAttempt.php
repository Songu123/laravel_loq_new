<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'score',
        'total_questions',
        'correct_answers',
        'percentage',
        'time_spent',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAttemptAnswer::class, 'attempt_id');
    }

    public function violations()
    {
        return $this->hasMany(ExamViolation::class, 'attempt_id');
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopePassed($query)
    {
        return $query->where('percentage', '>=', 50);
    }

    public function scopeFailed($query)
    {
        return $query->where('percentage', '<', 50);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessors
     */
    public function getIsPassedAttribute()
    {
        return $this->percentage >= 50;
    }

    public function getGradeAttribute()
    {
        if ($this->percentage >= 90) return 'A';
        if ($this->percentage >= 80) return 'B';
        if ($this->percentage >= 70) return 'C';
        if ($this->percentage >= 60) return 'D';
        if ($this->percentage >= 50) return 'E';
        return 'F';
    }

    public function getTimeTakenTextAttribute()
    {
        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;
        
        if ($minutes > 0) {
            return $minutes . ' phút ' . $seconds . ' giây';
        }
        
        return $seconds . ' giây';
    }

    /**
     * Methods
     */
    public function calculateScore()
    {
        $totalScore = 0;
        $correctAnswers = 0;

        foreach ($this->answers as $answer) {
            if ($answer->is_correct) {
                $totalScore += $answer->points_earned;
                $correctAnswers++;
            }
        }

        $this->update([
            'score' => $totalScore,
            'correct_answers' => $correctAnswers,
            'percentage' => $this->total_questions > 0 
                ? ($correctAnswers / $this->total_questions) * 100 
                : 0,
        ]);

        return $this;
    }

    public function markAsCompleted()
    {
        $this->update([
            'completed_at' => now(),
            'time_spent' => now()->diffInSeconds($this->started_at),
        ]);

        return $this;
    }
}
