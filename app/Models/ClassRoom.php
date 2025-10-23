<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'join_code',
        'teacher_id',
        'description',
        'is_active',
        'require_approval',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->join_code)) {
                $model->join_code = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(int $length = 6): string
    {
        do {
            $code = Str::upper(Str::random($length));
        } while (static::where('join_code', $code)->exists());
        return $code;
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_user', 'class_id', 'user_id')
            ->withTimestamps();
    }

    public function joinRequests()
    {
        return $this->hasMany(ClassJoinRequest::class, 'class_id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'class_exam', 'class_id', 'exam_id')
            ->withTimestamps();
    }
}
