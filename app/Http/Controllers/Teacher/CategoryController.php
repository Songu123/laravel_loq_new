<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories for teachers
     */
    public function index()
    {
        // Teachers can see all active categories (including their own)
        $categories = Category::active()
            ->with('creator')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12);
            
        return view('teacher.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('teacher.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'integer|min:0'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true; // Teachers can create active categories immediately
        $validated['sort_order'] = $validated['sort_order'] ?? 999; // Default to end

        Category::create($validated);

        return redirect()->route('teacher.categories.index')
            ->with('success', 'Danh mục đã được tạo thành công!');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        // Only show active categories to teachers
        if (!$category->is_active) {
            abort(404);
        }
        
        return view('teacher.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        // Teachers can only edit their own categories
        if ($category->created_by !== Auth::id()) {
            abort(403, 'Bạn chỉ có thể chỉnh sửa danh mục do mình tạo.');
        }
        
        return view('teacher.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        // Teachers can only edit their own categories
        if ($category->created_by !== Auth::id()) {
            abort(403, 'Bạn chỉ có thể chỉnh sửa danh mục do mình tạo.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'integer|min:0'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        // Keep category active after teacher edits
        $validated['is_active'] = true;

        $category->update($validated);

        return redirect()->route('teacher.categories.index')
            ->with('success', 'Danh mục đã được cập nhật thành công!');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Teachers can only delete their own categories
        if ($category->created_by !== Auth::id()) {
            abort(403, 'Bạn chỉ có thể xóa danh mục do mình tạo.');
        }

        $category->delete();

        return redirect()->route('teacher.categories.index')
            ->with('success', 'Danh mục đã được xóa thành công!');
    }

    /**
     * Get categories for AJAX requests (for creating exams/questions)
     */
    public function getCategories(Request $request)
    {
        $categories = Category::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'color', 'icon', 'is_active']);
            
        return response()->json($categories);
    }

    /**
     * Request approval for a category (submit for admin review)
     */
    public function requestApproval(Category $category)
    {
        if ($category->created_by !== Auth::id()) {
            abort(403);
        }

        // Could add a status field in future for tracking approval requests
        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu duyệt đã được gửi đến quản trị viên!'
        ]);
    }
}
