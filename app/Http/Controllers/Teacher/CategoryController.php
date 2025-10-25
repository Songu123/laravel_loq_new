<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Apply authorization middleware
     */
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    /**
     * Display a listing of categories for teachers
     */
    public function index()
    {
        // Teachers can only see their own categories
        $categories = Category::where('created_by', Auth::id())
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
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

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
        // Authorization handled by CategoryPolicy
        return view('teacher.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        // Authorization handled by CategoryPolicy
        return view('teacher.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        // Authorization handled by CategoryPolicy
        $validated = $request->validated();

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
        // Authorization handled by CategoryPolicy
        $category->delete();

        return redirect()->route('teacher.categories.index')
            ->with('success', 'Danh mục đã được xóa thành công!');
    }

    /**
     * Get categories for AJAX requests (for creating exams/questions)
     */
    public function getCategories(Request $request)
    {
        // Teachers can only use their own categories for exams/questions
        $categories = Category::where('created_by', Auth::id())
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
        // Authorization handled by CategoryPolicy
        $this->authorize('requestApproval', $category);

        // Could add a status field in future for tracking approval requests
        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu duyệt đã được gửi đến quản trị viên!'
        ]);
    }
}
