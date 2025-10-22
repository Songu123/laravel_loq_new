# Anti-Cheating System - Documentation

## 🔐 Overview
Comprehensive fraud detection system for online exams that monitors student behavior in real-time and flags suspicious activities.

## 📋 Features Implemented

### 1. Frontend Detection (JavaScript)

#### **Tab/Window Switching** (`tab_switch`)
- **Detection**: `visibilitychange` and `blur` events
- **Severity**: Medium (2)
- **Triggers**: When student switches tabs or windows
- **Action**: Logs violation, shows warning

#### **Copy/Paste/Cut Prevention** (`copy_paste`)
- **Detection**: `copy`, `paste`, `cut` events
- **Severity**: Medium (2)
- **Triggers**: When student attempts to copy/paste content
- **Action**: Prevents action, logs violation

#### **Right Click Detection** (`right_click`)
- **Detection**: `contextmenu` event
- **Severity**: Low (1)
- **Triggers**: When student right-clicks
- **Action**: Prevents context menu, logs violation

#### **Keyboard Shortcuts** (`keyboard_shortcut`)
- **Detection**: `keydown` event monitoring
- **Severity**: Medium-High (2-3)
- **Blocked Shortcuts**:
  - Ctrl+C, Ctrl+V, Ctrl+X (copy/paste)
  - F12, Ctrl+Shift+I, Ctrl+Shift+J (Developer Tools)
  - Ctrl+U (View Source)
  - PrintScreen (Screenshot)

#### **Fullscreen Exit** (`fullscreen_exit`)
- **Detection**: `fullscreenchange` event
- **Severity**: High (3)
- **Triggers**: When student exits fullscreen mode
- **Action**: Logs violation, attempts to re-enter fullscreen

#### **Mouse Leave Detection** (`mouse_leave`)
- **Detection**: `mouseleave` event with 3-second threshold
- **Severity**: Low (1)
- **Triggers**: When mouse leaves window for >3 seconds
- **Action**: Logs violation

#### **Multiple Tabs/Devices** (`multiple_devices`)
- **Detection**: localStorage monitoring
- **Severity**: Critical (4)
- **Triggers**: When exam is opened in multiple tabs
- **Action**: Auto-submits exam immediately

#### **Time Anomaly** (`time_anomaly`)
- **Detection**: Click activity monitoring
- **Severity**: Medium (2)
- **Triggers**: No activity for >2 minutes
- **Action**: Logs inactivity period

## 🗄️ Database Structure

### `exam_violations` Table
```sql
id                  BIGINT (Primary Key)
attempt_id          BIGINT (Foreign Key → exam_attempts)
user_id            BIGINT (Foreign Key → users)
exam_id            BIGINT (Foreign Key → exams)
violation_type     ENUM (9 types)
description        VARCHAR(500)
metadata           JSON (additional details)
severity           INTEGER (1-4 scale)
ip_address         VARCHAR(45)
user_agent         TEXT
violated_at        TIMESTAMP
created_at         TIMESTAMP
updated_at         TIMESTAMP
```

### Severity Levels
- **1 (Low)**: Minor infractions (right-click, brief mouse leave)
- **2 (Medium)**: Moderate concerns (tab switching, copy/paste)
- **3 (High)**: Serious violations (fullscreen exit, dev tools)
- **4 (Critical)**: Severe cheating (multiple devices, systematic patterns)

## 🎯 Auto-Flagging Rules

Attempts are automatically flagged when:
1. **≥5 total violations** of any type
2. **≥2 critical violations** (severity 4)
3. **≥3 high severity violations** (severity ≥3)

## 🔧 Backend API

### Log Violation Endpoint
```
POST /student/exams/log-violation
```
**Request Body:**
```json
{
  "attempt_id": 123,
  "exam_id": 456,
  "violation_type": "tab_switch",
  "description": "Người dùng chuyển tab/cửa sổ",
  "severity": 2,
  "metadata": {
    "timestamp": "2025-10-22T10:30:00",
    "currentQuestion": 5
  }
}
```

**Response:**
```json
{
  "success": true,
  "violation_id": 789,
  "total_violations": 3
}
```

### Get Attempt Violations
```
GET /student/violations/{attemptId}
```
**Response:**
```json
{
  "attempt": {...},
  "violations": [...],
  "statistics": {
    "total": 5,
    "by_severity": {
      "1": 2,
      "2": 2,
      "3": 1
    },
    "by_type": {...},
    "high_severity": 1
  }
}
```

## 👨‍🏫 Teacher Features

### Violation Dashboard
- View all flagged attempts
- Filter by exam, violation type, severity
- Real-time statistics
- Approve/reject functionality

**Route:** `/teacher/violations/flagged`

### Violation Report
- Detailed violation timeline
- Behavioral analysis
- Risk assessment
- Recommended actions

**Route:** `/teacher/violations/report/{attemptId}`

## 🚨 User Experience

### Warning System
- **Visual**: Toast notifications for each violation
- **Counter**: Shows violations count and remaining attempts
- **Limit**: Auto-submits after 10 violations
- **Progressive**: Warnings increase in urgency

### Fullscreen Enforcement
- Auto-requests fullscreen on exam start
- Logs exit as high-severity violation
- Attempts to re-enter fullscreen automatically
- Visual warning when exiting

## 📊 Model Methods

### ExamViolation Model

```php
// Constants
const TYPE_TAB_SWITCH = 'tab_switch';
const SEVERITY_LOW = 1;
const SEVERITY_CRITICAL = 4;

// Relationships
violation->attempt()
violation->user()
violation->exam()

// Helper Methods
getSeverityBadgeColor()  // Returns Bootstrap color
getSeverityText()        // Returns Vietnamese text
getViolationTypeText()   // Returns Vietnamese type name

// Scopes
bySeverity($severity)
byType($type)
highSeverity()          // severity >= 3
recent($days = 7)
```

## 🔍 Analysis Features

### Pattern Detection
- Tab switching frequency
- Time consistency analysis
- Answer pattern analysis
- Similarity comparison with other attempts
- Behavioral risk scoring

### Risk Assessment
Calculates risk score (0-100) based on:
- Total violations count
- Severity distribution
- Time patterns
- Answer accuracy vs speed
- Historical behavior

## 📱 Frontend Implementation

### Initialization
```javascript
initializeAntiCheating();
requestFullscreen();
```

### Violation Logging
```javascript
logViolation(type, description, severity, metadata);
```

### Warning Display
```javascript
showViolationWarning(message, count);
```

### Auto-Submit
Triggers when:
- 10 violations reached
- Multiple tabs detected
- Time expires

## 🎨 UI Components

### Student View (take.blade.php)
- Fullscreen exam interface
- Real-time violation counter
- Warning toast notifications
- Progress tracking

### Teacher View (flagged-attempts.blade.php)
- Statistics cards
- Filterable table
- Approve/reject actions
- Export functionality

## 🔐 Security Features

1. **CSRF Protection**: All POST requests require token
2. **Authentication**: Only logged-in students can take exams
3. **Authorization**: Teachers can only view their own exams
4. **IP Logging**: Tracks violation source
5. **User Agent**: Identifies device/browser
6. **Timestamp**: Precise violation timing

## 📝 Metadata Examples

### Tab Switch
```json
{
  "timestamp": "2025-10-22T10:30:00",
  "currentQuestion": 5
}
```

### Keyboard Shortcut
```json
{
  "key": "c",
  "timestamp": "2025-10-22T10:31:00"
}
```

### Time Anomaly
```json
{
  "inactiveSeconds": 150,
  "timestamp": "2025-10-22T10:35:00"
}
```

## 🚀 Usage

### For Students
1. Start exam → Auto-enters fullscreen
2. Take exam normally
3. Avoid detected behaviors
4. View violation count in warnings
5. Submit before reaching limit

### For Teachers
1. Navigate to "Vi phạm & Gian lận"
2. View flagged attempts
3. Click "View Report" for details
4. Approve/reject based on analysis
5. Export reports for records

## ⚙️ Configuration

### Adjustable Settings (in take.blade.php)
```javascript
let maxViolations = 10;          // Violations before auto-submit
let mouseLeaveTimeout = 3000;    // Mouse leave threshold (ms)
let inactivityThreshold = 120;   // Inactivity threshold (seconds)
```

### Auto-Flag Thresholds (in ViolationAnalysisController)
```php
$shouldFlag = 
    $violations->count() >= 5 ||
    $violations->where('severity', 4)->count() >= 2 ||
    $violations->where('severity', '>=', 3)->count() >= 3;
```

## 🔄 Future Enhancements

### Planned Features
- [ ] Webcam monitoring (face detection)
- [ ] Eye-tracking analysis
- [ ] Audio monitoring
- [ ] AI-based behavioral analysis
- [ ] Real-time teacher alerts
- [ ] Student appeal system
- [ ] Violation history dashboard
- [ ] Machine learning pattern detection

### Potential Improvements
- [ ] Configurable violation limits per exam
- [ ] Custom severity weights
- [ ] Whitelist for specific shortcuts
- [ ] Grace period for accidental violations
- [ ] Multi-language support
- [ ] Mobile device optimization
- [ ] Offline detection

## 📚 Migration

### Run Migration
```bash
php artisan migrate
```

### Rollback (if needed)
```bash
php artisan migrate:rollback
```

## 🧪 Testing

### Test Violation Logging
1. Open exam in student mode
2. Try forbidden actions:
   - Switch tabs (Alt+Tab)
   - Copy text (Ctrl+C)
   - Right-click
   - Open dev tools (F12)
   - Exit fullscreen (Esc)
3. Check console for logs
4. View violations in teacher dashboard

### Test Auto-Submit
1. Trigger 10+ violations rapidly
2. Verify automatic submission
3. Check database for violation records

## 🐛 Troubleshooting

### Violations Not Logging
- Check CSRF token in meta tag
- Verify route exists: `/student/exams/log-violation`
- Check browser console for errors
- Ensure attempt_id exists

### Fullscreen Not Working
- Check browser permissions
- Try different browser (Chrome recommended)
- Verify JavaScript not blocked

### Auto-Flag Not Working
- Check `is_flagged` column exists in exam_attempts
- Verify thresholds in controller
- Check violation severity values

## 📞 Support

For issues or questions:
1. Check this documentation
2. Review error logs: `storage/logs/laravel.log`
3. Check database: `exam_violations` table
4. Contact system administrator

---

**Created**: October 22, 2025  
**Version**: 1.0.0  
**Author**: Laravel Quiz System Team
