<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\TransactionController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Products API Routes
Route::apiResource('products', ProductController::class);

// Brands API Routes
Route::apiResource('brands', BrandController::class);

// Categories API Routes
Route::apiResource('categories', CategoryController::class);

// Users API Routes
Route::apiResource('users', UserController::class);

// Orders API Routes
Route::apiResource('orders', OrderController::class);


// Order Items API Routes
Route::apiResource('order-items', OrderItemController::class);

// Addresses API Routes
Route::apiResource('addresses', AddressController::class);

// Transactions API Routes
Route::apiResource('transactions', TransactionController::class);

// Wishlist API Routes
Route::apiResource('wishlists', App\Http\Controllers\Api\WishlistController::class);

// Additional specific routes for relationships
Route::get('products/{id}/brand', [ProductController::class, 'getBrand']);
Route::get('products/{id}/category', [ProductController::class, 'getCategory']);
Route::get('brands/{id}/products', [BrandController::class, 'getProducts']);
Route::get('categories/{id}/products', [CategoryController::class, 'getProducts']);
Route::get('users/{id}/orders', [UserController::class, 'getOrders']);
Route::get('users/{id}/addresses', [UserController::class, 'getAddresses']);
Route::get('orders/{id}/items', [OrderController::class, 'getOrderItems']);
Route::get('orders/{id}/transaction', [OrderController::class, 'getTransaction']);

// Wishlist specific routes
Route::get('users/{id}/wishlist', [App\Http\Controllers\Api\WishlistController::class, 'getUserWishlist']);
Route::get('products/{id}/wishlist-users', [App\Http\Controllers\Api\WishlistController::class, 'getProductWishlistUsers']);
Route::delete('users/{id}/wishlist/clear', [App\Http\Controllers\Api\WishlistController::class, 'clearUserWishlist']);
