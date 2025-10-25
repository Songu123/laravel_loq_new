<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_text',
        'question_type',
        'marks',
        'order',
        'explanation',
        'is_required'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'marks' => 'decimal:2'
    ];

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class)->orderBy('order');
    }

    public function correctAnswers()
    {
        return $this->hasMany(Answer::class)->where('is_correct', true);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('question_type', $type);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    // Accessors & Mutators
    public function getQuestionTypeTextAttribute()
    {
        return match($this->question_type) {
            'multiple_choice' => 'Trắc nghiệm',
            'true_false' => 'Đúng/Sai',
            'short_answer' => 'Câu trả lời ngắn',
            'essay' => 'Tự luận',
            default => 'Không xác định'
        };
    }

    // Helper methods
    public function hasCorrectAnswer()
    {
        return $this->answers()->where('is_correct', true)->exists();
    }

    public function getCorrectAnswerIds()
    {
        return $this->answers()->where('is_correct', true)->pluck('id')->toArray();
    }

    public function isMultipleChoice()
    {
        return $this->question_type === 'multiple_choice';
    }

    public function isTrueFalse()
    {
        return $this->question_type === 'true_false';
    }

    public function isShortAnswer()
    {
        return $this->question_type === 'short_answer';
    }

    public function isEssay()
    {
        return $this->question_type === 'essay';
    }
}
