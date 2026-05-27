<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search by name or SKU
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Only show products with stock > 0
        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $products = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
        ]);

        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('category'),
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'product' => $product,
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $id,
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'image' => 'nullable|string',
        ]);

        $product->update($request->all());

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->load('category'),
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Check if product has transactions
        if ($product->transactionDetails()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete product with existing transactions',
            ], 422);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}