<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassJoinRequest;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::where('teacher_id', Auth::id())
            ->latest()
            ->paginate(12);
        return view('teacher.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('teacher.classes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'require_approval' => 'nullable|boolean',
        ]);

        $class = ClassRoom::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'teacher_id' => Auth::id(),
            'is_active' => true,
            'require_approval' => $request->boolean('require_approval', true),
        ]);

        return redirect()->route('teacher.classes.index')
            ->with('success', 'Tạo lớp thành công. Mã tham gia: ' . $class->join_code);
    }

    public function show(ClassRoom $class)
    {
        $this->authorizeTeacher($class);
        $class->load(['students', 'joinRequests' => function($q){ $q->latest(); }, 'exams']);
        $teacherExams = Exam::where('created_by', Auth::id())->orderBy('title')->get();
        return view('teacher.classes.show', compact('class', 'teacherExams'));
    }

    public function regenerateCode(ClassRoom $class)
    {
        $this->authorizeTeacher($class);
        $class->join_code = ClassRoom::generateUniqueCode();
        $class->save();
        return back()->with('success', 'Đã tạo mã tham gia mới: ' . $class->join_code);
    }

    public function toggleApproval(ClassRoom $class)
    {
        $this->authorizeTeacher($class);
        $class->require_approval = !$class->require_approval;
        $class->save();
        $mode = $class->require_approval ? 'Bật duyệt trước khi vào lớp' : 'Tắt duyệt (tự động tham gia)';
        return back()->with('success', 'Đã cập nhật chế độ tham gia: ' . $mode);
    }

    public function approveRequest(ClassRoom $class, ClassJoinRequest $requestItem)
    {
        $this->authorizeTeacher($class);
        if ($requestItem->class_id !== $class->id) abort(404);

        DB::beginTransaction();
        try {
            // attach student
            $class->students()->syncWithoutDetaching([$requestItem->student_id => ['joined_at' => now()]]);
            // update request
            $requestItem->update([
                'status' => 'approved',
                'decided_by' => Auth::id(),
                'decided_at' => now(),
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Không thể duyệt yêu cầu: ' . $e->getMessage());
        }

        // notify student
        try {
            Notification::send($requestItem->student, new \App\Notifications\JoinRequestApproved(
                classId: $class->id,
                className: $class->name,
            ));
        } catch (\Throwable $e) { report($e); }

        return back()->with('success', 'Đã chấp thuận yêu cầu tham gia lớp.');
    }

    public function rejectRequest(ClassRoom $class, ClassJoinRequest $requestItem, Request $request)
    {
        $this->authorizeTeacher($class);
        if ($requestItem->class_id !== $class->id) abort(404);

        $note = $request->input('note');
        $requestItem->update([
            'status' => 'rejected',
            'decided_by' => Auth::id(),
            'decided_at' => now(),
            'note' => $note,
        ]);

        try {
            Notification::send($requestItem->student, new \App\Notifications\JoinRequestRejected(
                classId: $class->id,
                className: $class->name,
                note: $note,
            ));
        } catch (\Throwable $e) { report($e); }

        return back()->with('success', 'Đã từ chối yêu cầu tham gia lớp.');
    }

    public function removeStudent(ClassRoom $class, Request $request)
    {
        $this->authorizeTeacher($class);
        $studentId = (int) $request->input('user_id');
        $class->students()->detach($studentId);
        return back()->with('success', 'Đã xóa học sinh khỏi lớp.');
    }

    public function attachExam(ClassRoom $class, Request $request)
    {
        $this->authorizeTeacher($class);
        $examId = (int) $request->input('exam_id');
        $exam = Exam::where('id', $examId)->where('created_by', Auth::id())->firstOrFail();
        $class->exams()->syncWithoutDetaching([$exam->id]);
        return back()->with('success', 'Đã thêm đề thi vào lớp.');
    }

    public function detachExam(ClassRoom $class, Request $request)
    {
        $this->authorizeTeacher($class);
        $examId = (int) $request->input('exam_id');
        $class->exams()->detach($examId);
        return back()->with('success', 'Đã gỡ đề thi khỏi lớp.');
    }

    private function authorizeTeacher(ClassRoom $class)
    {
        if ($class->teacher_id !== Auth::id()) {
            abort(403);
        }
    }
}
