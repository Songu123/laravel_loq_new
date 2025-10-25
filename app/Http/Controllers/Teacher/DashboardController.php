<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Category;
use App\Models\ClassRoom;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Statistics
        $totalExams = Exam::where('created_by', $userId)->count();
        
        // Total students from all classes
        $totalStudents = ClassRoom::where('teacher_id', $userId)
            ->withCount('students')
            ->get()
            ->sum('students_count');
        
        // Ongoing exams (active and within time range)
        $ongoingExams = Exam::where('created_by', $userId)
            ->where('is_active', true)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->whereNull('start_time')
                      ->orWhere('start_time', '<=', now());
                })
                ->where(function($q) {
                    $q->whereNull('end_time')
                      ->orWhere('end_time', '>=', now());
                });
            })
            ->count();
        
        // Completed attempts across all teacher's exams
        $teacherExamIds = Exam::where('created_by', $userId)->pluck('id');
        $completedAttempts = ExamAttempt::whereIn('exam_id', $teacherExamIds)
            ->whereNotNull('completed_at')
            ->count();

        // Categories with statistics (only teacher's own categories)
        $categories = Category::where('created_by', $userId)
            ->withCount([
                'exams as teacher_exams_count' => function($query) use ($userId) {
                    $query->where('created_by', $userId);
                },
                'exams as total_questions_count' => function($query) use ($userId) {
                    $query->where('created_by', $userId)
                          ->join('questions', 'exams.id', '=', 'questions.exam_id')
                          ->select(DB::raw('COUNT(questions.id)'));
                }
            ])
            ->with(['exams' => function($query) use ($userId) {
                $query->where('created_by', $userId)
                      ->withCount('attempts');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function($category) use ($userId) {
                // Calculate total students who took exams in this category
                $examIds = $category->exams->pluck('id');
                $category->students_count = ExamAttempt::whereIn('exam_id', $examIds)
                    ->distinct('user_id')
                    ->count('user_id');
                
                // Calculate total questions
                $category->questions_count = DB::table('questions')
                    ->whereIn('exam_id', $examIds)
                    ->count();
                
                return $category;
            });

        // Recent exams
        $recentExams = Exam::where('created_by', $userId)
            ->with(['category'])
            ->withCount('questions', 'attempts')
            ->latest()
            ->take(5)
            ->get();

        // Recent activity (attempts)
        $recentAttempts = ExamAttempt::whereIn('exam_id', $teacherExamIds)
            ->with(['exam', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('teacher-dashboard', compact(
            'totalExams',
            'totalStudents',
            'ongoingExams',
            'completedAttempts',
            'categories',
            'recentExams',
            'recentAttempts'
        ));
    }
}
