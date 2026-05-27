<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::withCount('products')->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category->loadCount('products'),
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show(string $id)
    {
        $category = Category::with(['products'])->withCount('products')->findOrFail($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category->loadCount('products'),
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with existing products',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}