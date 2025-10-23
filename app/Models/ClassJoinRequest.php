<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassJoinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'student_id',
        'status',
        'decided_by',
        'decided_at',
        'note',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
