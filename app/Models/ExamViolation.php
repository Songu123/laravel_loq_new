<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'user_id',
        'exam_id',
        'violation_type',
        'description',
        'metadata',
        'severity',
        'ip_address',
        'user_agent',
        'violated_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'violated_at' => 'datetime',
    ];

    /**
     * Violation types constants
     */
    const TYPE_TAB_SWITCH = 'tab_switch';
    const TYPE_COPY_PASTE = 'copy_paste';
    const TYPE_FULLSCREEN_EXIT = 'fullscreen_exit';
    const TYPE_MOUSE_LEAVE = 'mouse_leave';
    const TYPE_RIGHT_CLICK = 'right_click';
    const TYPE_KEYBOARD_SHORTCUT = 'keyboard_shortcut';
    const TYPE_TIME_ANOMALY = 'time_anomaly';
    const TYPE_MULTIPLE_DEVICES = 'multiple_devices';
    const TYPE_SUSPICIOUS_PATTERN = 'suspicious_pattern';

    /**
     * Severity levels
     */
    const SEVERITY_LOW = 1;
    const SEVERITY_MEDIUM = 2;
    const SEVERITY_HIGH = 3;
    const SEVERITY_CRITICAL = 4;

    /**
     * Get the attempt that owns the violation
     */
    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    /**
     * Get the user that owns the violation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exam that owns the violation
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get severity badge color
     */
    public function getSeverityBadgeColor()
    {
        return match($this->severity) {
            self::SEVERITY_LOW => 'info',
            self::SEVERITY_MEDIUM => 'warning',
            self::SEVERITY_HIGH => 'danger',
            self::SEVERITY_CRITICAL => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Get severity text
     */
    public function getSeverityText()
    {
        return match($this->severity) {
            self::SEVERITY_LOW => 'Thấp',
            self::SEVERITY_MEDIUM => 'Trung bình',
            self::SEVERITY_HIGH => 'Cao',
            self::SEVERITY_CRITICAL => 'Nghiêm trọng',
            default => 'Không xác định'
        };
    }

    /**
     * Get violation type text
     */
    public function getViolationTypeText()
    {
        return match($this->violation_type) {
            self::TYPE_TAB_SWITCH => 'Chuyển tab',
            self::TYPE_COPY_PASTE => 'Sao chép/Dán',
            self::TYPE_FULLSCREEN_EXIT => 'Thoát fullscreen',
            self::TYPE_MOUSE_LEAVE => 'Chuột rời màn hình',
            self::TYPE_RIGHT_CLICK => 'Click chuột phải',
            self::TYPE_KEYBOARD_SHORTCUT => 'Phím tắt đáng ngờ',
            self::TYPE_TIME_ANOMALY => 'Thời gian bất thường',
            self::TYPE_MULTIPLE_DEVICES => 'Nhiều thiết bị',
            self::TYPE_SUSPICIOUS_PATTERN => 'Hành vi đáng ngờ',
            default => 'Không xác định'
        };
    }

    /**
     * Scope to filter by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope to filter by violation type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('violation_type', $type);
    }

    /**
     * Scope to get high severity violations
     */
    public function scopeHighSeverity($query)
    {
        return $query->where('severity', '>=', self::SEVERITY_HIGH);
    }

    /**
     * Scope to get recent violations
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('violated_at', '>=', now()->subDays($days));
    }
}
