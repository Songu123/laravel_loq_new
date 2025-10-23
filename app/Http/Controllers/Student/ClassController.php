<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassJoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ClassController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $classes = $user->joinedClasses()->latest()->paginate(12);
        return view('student.classes.index', compact('classes'));
    }

    public function joinForm()
    {
        return view('student.classes.join');
    }

    public function show(\App\Models\ClassRoom $class)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // authorize membership
        $isMember = $user->joinedClasses()->where('classes.id', $class->id)->exists();
        if (!$isMember) abort(403);

        $class->load(['teacher', 'exams.category']);
        return view('student.classes.show', compact('class'));
    }

    public function join(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:12',
        ]);

        $class = ClassRoom::where('join_code', strtoupper($data['code']))
            ->where('is_active', true)
            ->first();

        if (!$class) {
            return back()->with('error', 'Mã lớp không hợp lệ hoặc lớp đã đóng.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$class->require_approval) {
            // Auto-join when approval not required
            DB::beginTransaction();
            try {
                $user->joinedClasses()->syncWithoutDetaching([
                    $class->id => [
                        'joined_at' => now(),
                    ]
                ]);
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->with('error', 'Không thể tham gia lớp: ' . $e->getMessage());
            }
            return redirect()->route('student.classes.index')->with('success', 'Đã tham gia lớp: ' . $class->name);
        }

        // With approval: create pending request
        $requestExists = ClassJoinRequest::where('class_id', $class->id)
            ->where('student_id', $user->id)
            ->first();

        if ($requestExists) {
            return redirect()->route('student.classes.index')
                ->with('success', 'Yêu cầu tham gia lớp đã được gửi. Vui lòng chờ giáo viên duyệt.');
        }

        $joinReq = ClassJoinRequest::create([
            'class_id' => $class->id,
            'student_id' => $user->id,
            'status' => 'pending',
        ]);

        // Notify teacher
        try {
            if ($class->teacher) {
                Notification::send($class->teacher, new \App\Notifications\StudentJoinRequested(
                    classId: $class->id,
                    className: $class->name,
                    studentId: $user->id,
                    studentName: $user->name,
                ));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('student.classes.index')
            ->with('success', 'Đã gửi yêu cầu tham gia lớp. Vui lòng chờ giáo viên duyệt.');
    }
}
