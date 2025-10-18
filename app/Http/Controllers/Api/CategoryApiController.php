<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    /**
     * Get active categories for dropdowns
     */
    public function index(Request $request)
    {
        $query = Category::active();

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Limit
        $limit = $request->get('limit', 50);
        
        $categories = $query->orderBy('name')->take($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'icon' => $category->icon,
                    'color' => $category->color,
                ];
            })
        ]);
    }

    /**
     * Get single category
     */
    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'icon' => $category->icon,
                'color' => $category->color,
            ]
        ]);
    }
}