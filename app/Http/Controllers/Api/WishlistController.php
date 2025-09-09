<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class WishlistController extends Controller
{
    /**
     * Display a listing of all wishlist items.
     */
    public function index(): JsonResponse
    {
        try {
            $wishlists = Wishlist::with(['user', 'product'])->get();
            
            return response()->json([
                'success' => true,
                'data' => $wishlists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wishlist items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created wishlist item.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'product_id' => 'required|exists:products,id'
            ]);

            // Check if the wishlist item already exists
            $existingWishlistItem = Wishlist::where('user_id', $validated['user_id'])
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($existingWishlistItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is already in the wishlist'
                ], 409);
            }

            $wishlist = Wishlist::create($validated);
            $wishlist->load(['user', 'product']);

            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist successfully',
                'data' => $wishlist
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
                'message' => 'Failed to add product to wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified wishlist item.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $wishlist = Wishlist::with(['user', 'product'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $wishlist
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found'
            ], 404);
        }
    }

    /**
     * Update the specified wishlist item.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $wishlist = Wishlist::findOrFail($id);

            $validated = $request->validate([
                'user_id' => 'sometimes|exists:users,id',
                'product_id' => 'sometimes|exists:products,id'
            ]);

            // Check for duplicate if updating user_id or product_id
            if (isset($validated['user_id']) || isset($validated['product_id'])) {
                $userId = $validated['user_id'] ?? $wishlist->user_id;
                $productId = $validated['product_id'] ?? $wishlist->product_id;

                $existingWishlistItem = Wishlist::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingWishlistItem) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product is already in the wishlist for this user'
                    ], 409);
                }
            }

            $wishlist->update($validated);
            $wishlist->load(['user', 'product']);

            return response()->json([
                'success' => true,
                'message' => 'Wishlist item updated successfully',
                'data' => $wishlist
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
                'message' => 'Failed to update wishlist item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified wishlist item.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $wishlist = Wishlist::findOrFail($id);
            $wishlist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Wishlist item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete wishlist item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wishlist items for a specific user.
     */
    public function getUserWishlist(string $userId): JsonResponse
    {
        try {
            $wishlists = Wishlist::with(['product.category', 'product.brand'])
                ->where('user_id', $userId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $wishlists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users who have a specific product in their wishlist.
     */
    public function getProductWishlistUsers(string $productId): JsonResponse
    {
        try {
            $wishlists = Wishlist::with(['user'])
                ->where('product_id', $productId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $wishlists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product wishlist users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove all wishlist items for a specific user.
     */
    public function clearUserWishlist(string $userId): JsonResponse
    {
        try {
            $deletedCount = Wishlist::where('user_id', $userId)->delete();

            return response()->json([
                'success' => true,
                'message' => "Cleared {$deletedCount} items from wishlist",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear user wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}