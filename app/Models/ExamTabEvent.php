<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamTabEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'exam_id', 'attempt_id', 'event_type', 'event_data',
        'ip_address', 'user_agent', 'occurred_at'
    ];

    protected $casts = [
        'event_data' => 'array',
        'occurred_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    // Scopes
    public function scopeTabSwitches($query, $attemptId)
    {
        return $query->where('attempt_id', $attemptId)
                    ->whereIn('event_type', ['tab_switch', 'window_blur', 'visibility_change'])
                    ->where('event_data->action', 'blur');
    }

    public function scopeSuspicious($query, $attemptId, $threshold = 3)
    {
        return $query->where('attempt_id', $attemptId)
                    ->whereIn('event_type', ['tab_switch', 'window_blur'])
                    ->havingRaw('COUNT(*) > ?', [$threshold]);
    }

    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    // Accessors
    public function getEventDescriptionAttribute()
    {
        switch ($this->event_type) {
            case 'visibility_change':
                return $this->event_data['action'] === 'hidden' 
                    ? 'Chuyển sang tab khác' 
                    : 'Quay lại trang thi';
            case 'window_blur':
                return 'Rời khỏi cửa sổ thi';
            case 'window_focus':
                return 'Quay lại cửa sổ thi';
            case 'suspicious_key':
                return 'Nhấn phím nghi vấn: ' . ($this->event_data['key'] ?? '');
            default:
                return ucfirst($this->event_type);
        }
    }

    public function getSeverityAttribute()
    {
        if ($this->event_type === 'suspicious_key') return 'high';
        if ($this->event_type === 'visibility_change' && $this->event_data['action'] === 'hidden') return 'medium';
        return 'low';
    }

    public function getSeverityLabelAttribute()
    {
        $labels = [
            'high' => 'Cao',
            'medium' => 'Trung bình',
            'low' => 'Thấp'
        ];
        return $labels[$this->getSeverityAttribute()] ?? 'Không xác định';
    }
}