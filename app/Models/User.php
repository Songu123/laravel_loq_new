<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Contracts\Auth\MustVerifyEmail; // ✅ Thêm use  
class User extends Authenticatable implements MustVerifyEmail // ✅ Thêm implements
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email', 
        'password',
        'provider',
        'provider_id',
        'google_id',
        'facebook_id',
        'role',
        'student_id',
        'phone',
        'address',
        'bio',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is teacher
     */
    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is student
     */
    public function isStudent()
    {
        return $this->role === 'student';
    }

    /**
     * Get display role name
     */
    public function getRoleDisplayName()
    {
        return match($this->role) {
            'admin' => 'Quản trị viên',
            'teacher' => 'Giáo viên',
            'student' => 'Học sinh',
            default => 'Không xác định'
        };
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeColor()
    {
        return match($this->role) {
            'admin' => 'danger',
            'teacher' => 'warning',
            'student' => 'primary',
            default => 'secondary'
        };
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrl()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Default avatar based on role
        return match($this->role) {
            'admin' => asset('images/default-admin-avatar.png'),
            'teacher' => asset('images/default-teacher-avatar.png'),
            'student' => asset('images/default-student-avatar.png'),
            default => asset('images/default-avatar.png')
        };
    }

    /**
     * Scope to filter by role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to get active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get verified users
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Get full name with role
     */
    public function getFullNameWithRole()
    {
        return $this->name . ' (' . $this->getRoleDisplayName() . ')';
    }

    /**
     * Get all exam attempts by this user
     */
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Classes this user teaches (for teachers)
     */
    public function taughtClasses()
    {
        return $this->hasMany(\App\Models\ClassRoom::class, 'teacher_id');
    }

    /**
     * Classes this user joined (for students)
     */
    public function joinedClasses()
    {
        return $this->belongsToMany(\App\Models\ClassRoom::class, 'class_user', 'user_id', 'class_id')
            ->withTimestamps();
    }
}
