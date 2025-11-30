<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource
     */
    public function index(): JsonResponse
    {
        try {
            $products = Product::with(['category', 'brand'])->get();
            
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|unique:products,slug',
                'short_description' => 'nullable|string',
                'description' => 'nullable|string',
                'regular_price' => 'required|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
                'SKU' => 'required|string|unique:products,SKU',
                'stock_status' => 'required|in:instock,outofstock',
                'featured' => 'boolean',
                'quantity' => 'required|integer|min:0',
                'image' => 'nullable|string',
                'gallery' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id'
            ]);

            $product = Product::create($validated);
            $product->load(['category', 'brand']);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = Product::with(['category', 'brand'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'slug' => 'sometimes|required|string|unique:products,slug,' . $id,
                'short_description' => 'nullable|string',
                'description' => 'nullable|string',
                'regular_price' => 'sometimes|required|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
                'SKU' => 'sometimes|required|string|unique:products,SKU,' . $id,
                'stock_status' => 'sometimes|required|in:instock,outofstock',
                'featured' => 'boolean',
                'quantity' => 'sometimes|required|integer|min:0',
                'image' => 'nullable|string',
                'gallery' => 'nullable|string',
                'category_id' => 'sometimes|required|exists:categories,id',
                'brand_id' => 'sometimes|required|exists:brands,id'
            ]);

            $product->update($validated);
            $product->load(['category', 'brand']);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product's brand
     */
    public function getBrand(string $id): JsonResponse
    {
        try {
            $product = Product::with('brand')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product->brand
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }

    /**
     * Get product's category
     */
    public function getCategory(string $id): JsonResponse
    {
        try {
            $product = Product::with('category')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product->category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }
}
