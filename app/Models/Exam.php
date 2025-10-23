<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'created_by',
        'duration_minutes',
        'total_questions',
        'total_marks',
        'difficulty_level',
        'is_active',
        'is_public',
        'start_time',
        'end_time',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'total_marks' => 'decimal:2'
    ];

    // Auto-generate slug when creating
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($exam) {
            if (empty($exam->slug)) {
                $exam->slug = Str::slug($exam->title);
                
                // Ensure unique slug
                $originalSlug = $exam->slug;
                $counter = 1;
                while (static::where('slug', $exam->slug)->exists()) {
                    $exam->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        static::updating(function ($exam) {
            if ($exam->isDirty('title') && empty($exam->getOriginal('slug'))) {
                $exam->slug = Str::slug($exam->title);
            }
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function classes()
    {
        return $this->belongsToMany(\App\Models\ClassRoom::class, 'class_exam', 'exam_id', 'class_id')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    // Accessors & Mutators
    public function getDifficultyLevelTextAttribute()
    {
        return match($this->difficulty_level) {
            'easy' => 'Dễ',
            'medium' => 'Trung bình',
            'hard' => 'Khó',
            default => 'Không xác định'
        };
    }

    public function getDurationTextAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return $hours . ' giờ' . ($minutes > 0 ? ' ' . $minutes . ' phút' : '');
        }
        
        return $minutes . ' phút';
    }

    // Helper methods
    public function updateStats()
    {
        $this->total_questions = $this->questions()->count();
        $this->total_marks = $this->questions()->sum('marks');
        $this->save();
    }

    public function canEdit($user)
    {
        return $user->isAdmin() || $this->created_by === $user->id;
    }

    public function isAvailable()
    {
        $now = now();
        
        if ($this->start_time && $now->lt($this->start_time)) {
            return false;
        }
        
        if ($this->end_time && $now->gt($this->end_time)) {
            return false;
        }
        
        return $this->is_active;
    }

    /**
     * Eligible students for this exam: all students in attached classes.
     * If no classes are attached and exam is public, fall back to all active students.
     */
    public function eligibleStudents()
    {
        $classIds = $this->classes()->pluck('classes.id');
        if ($classIds->isNotEmpty()) {
            return \App\Models\User::query()
                ->where('role', 'student')
                ->whereHas('joinedClasses', function ($q) use ($classIds) {
                    $q->whereIn('classes.id', $classIds);
                });
        }

        if ($this->is_public) {
            return \App\Models\User::query()->where('role', 'student');
        }

        // No classes and not public -> no eligible students
        return \App\Models\User::query()->whereRaw('1=0');
    }
}
