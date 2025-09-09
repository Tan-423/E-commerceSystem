<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-qunatity/{rowId}',[CartController::class,'increase_cart_quantity'])->name('cart.qty.increase');
Route::put('/cart/reduce-qunatity/{rowId}',[CartController::class,'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}',[CartController::class,'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear',[CartController::class,'empty_cart'])->name('cart.empty');
Route::get('/checkout',[CartController::class,'checkout'])->name('cart.checkout');
Route::post('/place-order',[CartController::class,'place_order'])->name('cart.place.order');
Route::get('/order-confirmation',[CartController::class,'order_confirmation'])->name('cart.order.confirmation');
Route::get('/paypal/success', [CartController::class, 'paypal_success'])->name('paypal.success');
Route::get('/paypal/cancel', [CartController::class, 'paypal_cancel'])->name('paypal.cancel');


Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-orders/{order_id}/details', [UserController::class, 'order_details'])->name('user.order.details');
    Route::put('/account-orders/cancel', [UserController::class, 'order_cancel'])->name('user.order.cancel');
    
    // Wishlist routes
    Route::get('/account-wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('user.wishlist');
    Route::post('/wishlist/add', [App\Http\Controllers\WishlistController::class, 'addToWishlist'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{product_id}', [App\Http\Controllers\WishlistController::class, 'removeFromWishlist'])->name('wishlist.remove');
    Route::delete('/wishlist/clear', [App\Http\Controllers\WishlistController::class, 'clearWishlist'])->name('wishlist.clear');
    Route::get('/wishlist/count', [App\Http\Controllers\WishlistController::class, 'getWishlistCount'])->name('wishlist.count');
    Route::get('/wishlist/check/{product_id}', [App\Http\Controllers\WishlistController::class, 'isInWishlist'])->name('wishlist.check');
    Route::post('/wishlist/move-to-cart', [App\Http\Controllers\WishlistController::class, 'moveToCart'])->name('wishlist.move.cart');
});

Route::middleware(['auth', AuthAdmin::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    Route::get('/admin/brands', [BrandController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brands/add', [BrandController::class, 'add_brand'])->name('admin.brand.add');
    Route::post('/admin/brands/store', [BrandController::class, 'brand_store'])->name('admin.brand.store');
    Route::get('/admin/brands/edit/{id}', [BrandController::class, 'brand_edit'])->name('admin.brand.edit');
    Route::put('/admin/brands/update', [BrandController::class, 'brand_update'])->name('admin.brand.update');
    Route::delete('/admin/brands/{id}/delete', [BrandController::class, 'brand_delete'])->name('admin.brand.delete');

    Route::get('/admin/categories', [CategoryController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category/add', [CategoryController::class, 'category_add'])->name('admin.category.add');
    Route::post('/admin/category/store', [CategoryController::class, 'category_store'])->name('admin.category.store');
    Route::get('/admin/category/edit/{id}', [CategoryController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('/admin/category/update', [CategoryController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/admin/category/{id}/delete', [CategoryController::class, 'category_delete'])->name('admin.category.delete');

    Route::get('/admin/products', [ProductController::class, 'products'])->name('admin.products');
    Route::get('/admin/products/add', [ProductController::class, 'add_product'])->name('admin.product.add');
    Route::post('/admin/products/store', [ProductController::class, 'product_store'])->name('admin.product.store');
    Route::get('/admin/product/{id}/edit', [ProductController::class, 'edit_product'])->name('admin.product.edit');
    Route::put('/admin/product/update', [ProductController::class, 'update_product'])->name('admin.product.update');
    Route::delete('/admin/product/{id}/delete', [ProductController::class, 'delete_product'])->name('admin.product.delete');

    Route::get('/admin/orders',[AdminController::class,'orders'])->name('admin.orders');
    Route::get('/admin/order/{order_id}/details',[AdminController::class,'order_details'])->name('admin.order_details');
    Route::put('/admin/order/update-status',[AdminController::class,'update_order_status'])->name('admin.order.status.update');

    Route::get('/admin/users',[AdminController::class,'users'])->name('admin.users');
    Route::put('/admin/user/{user_id}/lock',[AdminController::class,'lock_user'])->name('admin.user.lock');
    Route::put('/admin/user/{user_id}/unlock',[AdminController::class,'unlock_user'])->name('admin.user.unlock');

    Route::get('/admin/payments',[AdminController::class,'payments'])->name('admin.payments');

});
