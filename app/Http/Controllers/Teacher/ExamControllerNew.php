<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Category;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->middleware('auth');
        $this->middleware('teacher');
        $this->examService = $examService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['category', 'creator'])
                     ->withCount('questions')
                     ->where('created_by', Auth::id());

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $exams = $query->paginate(12);
        $categories = Category::active()->orderBy('name')->get();

        return view('teacher.exams.index', compact('exams', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('teacher.exams.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamRequest $request)
    {
        try {
            $exam = $this->examService->createExam($request->validated());

            return redirect()->route('teacher.exams.show', $exam)
                           ->with('success', 'Đề thi đã được tạo thành công!');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->withErrors(['error' => 'Có lỗi xảy ra khi tạo đề thi: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        // Check ownership
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập đề thi này.');
        }

        $exam->load(['category', 'creator', 'questions.answers']);
        $stats = $this->examService->getExamStats($exam);

        return view('teacher.exams.show', compact('exam', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        // Check ownership
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa đề thi này.');
        }

        $exam->load(['questions.answers']);
        $categories = Category::active()->orderBy('name')->get();

        return view('teacher.exams.edit', compact('exam', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        // Check ownership
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền cập nhật đề thi này.');
        }

        try {
            $exam = $this->examService->updateExam($exam, $request->validated());

            return redirect()->route('teacher.exams.show', $exam)
                           ->with('success', 'Đề thi đã được cập nhật thành công!');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật đề thi: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        // Check ownership
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa đề thi này.');
        }

        try {
            $this->examService->deleteExam($exam);

            return redirect()->route('teacher.exams.index')
                           ->with('success', 'Đề thi đã được xóa thành công!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi xóa đề thi: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle exam status (active/inactive)
     */
    public function toggleStatus(Exam $exam)
    {
        // Check ownership
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền thay đổi trạng thái đề thi này.');
        }

        try {
            $this->examService->toggleStatus($exam);

            return back()->with('success', 'Trạng thái đề thi đã được cập nhật!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Duplicate an exam
     */
    public function duplicate(Exam $exam)
    {
        // Check ownership
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'Bạn không có quyền sao chép đề thi này.');
        }

        try {
            $newExam = $this->examService->duplicateExam($exam);

            return redirect()->route('teacher.exams.edit', $newExam)
                           ->with('success', 'Đề thi đã được sao chép thành công! Vui lòng chỉnh sửa và kích hoạt.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi sao chép đề thi: ' . $e->getMessage()]);
        }
    }
}