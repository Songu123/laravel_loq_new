<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Category;
use App\Models\ExamAttempt;

class HomeController extends Controller
{
    /**
     * Show the application home page with public exams
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Get public exams
        $query = Exam::with(['category', 'creator'])
            ->where('is_active', true)
            ->where('is_public', true);

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // Sort
        switch ($request->get('sort', 'newest')) {
            case 'popular':
                $query->withCount('attempts')->orderByDesc('attempts_count');
                break;
            case 'easiest':
                $query->orderBy('difficulty_level', 'asc');
                break;
            case 'hardest':
                $query->orderBy('difficulty_level', 'desc');
                break;
            default:
                $query->latest();
        }

        $exams = $query->paginate(12)->withQueryString();

        // Get categories for filter
        $categories = Category::where('is_active', true)->get();

        // Stats
        $stats = [
            'total_exams' => Exam::where('is_active', true)->where('is_public', true)->count(),
            'total_categories' => Category::where('is_active', true)->count(),
            'total_attempts' => ExamAttempt::count(),
        ];

        return view('home', compact('exams', 'categories', 'stats'));
    }
}

