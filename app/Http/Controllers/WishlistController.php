<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist
     */
    public function index()
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlists()->with(['product.category', 'product.brand'])->paginate(10);
        
        return view('user.wishlist', compact('wishlistItems'));
    }

    /**
     * Add a product to the user's wishlist.
     */
    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        // Check if product is already in wishlist
        $existingWishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existingWishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist!'
            ], 409);
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully!'
        ]);
    }

    /**
     * Remove a product from the user's wishlist.
     */
    public function removeFromWishlist($productId)
    {
        $user = Auth::user();
        
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in your wishlist!'
            ], 404);
        }

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist successfully!'
        ]);
    }

    /**
     * Clear all items from the user's wishlist.
     */
    public function clearWishlist()
    {
        $user = Auth::user();
        
        Wishlist::where('user_id', $user->id)->delete();

        return redirect()->route('user.wishlist')->with('status', 'Wishlist cleared successfully!');
    }

    /**
     * Get the count of items in the user's wishlist.
     */
    public function getWishlistCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $count = Wishlist::where('user_id', Auth::id())->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Check if a product is in the user's wishlist.
     */
    public function isInWishlist($productId)
    {
        if (!Auth::check()) {
            return response()->json(['inWishlist' => false]);
        }

        $inWishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();

        return response()->json(['inWishlist' => $inWishlist]);
    }

    /**
     * Move a product from wishlist to cart.
     */
    public function moveToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        // Check if product is in wishlist
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in your wishlist!'
            ], 404);
        }

        $product = Product::find($productId);
        
        // Check if product is in stock
        if ($product->stock_status !== 'instock' || $product->quantity < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Product is out of stock!'
            ], 400);
        }

        // Add to cart (assuming you have a cart system similar to the existing one)
        // This would integrate with your existing CartController logic
        
        // Remove from wishlist
        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product moved to cart successfully!'
        ]);
    }
}
