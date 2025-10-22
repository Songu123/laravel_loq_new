<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;
    
    protected $table = 'exam_attempt_answers'; // Use correct table name
    
    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_id',
        'answer_text',
        'is_correct',
        'points_earned'
    ];
    
    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'decimal:2'
    ];
    
    /**
     * Relationship: ExamAnswer belongs to ExamAttempt
     */
    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }
    
    /**
     * Relationship: ExamAnswer belongs to Question
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    
    /**
     * Relationship: ExamAnswer belongs to Answer (selected answer)
     */
    public function answer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }
}
