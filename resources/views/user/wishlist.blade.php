@extends('layouts.app')
@section('content')

<style>
    .table> :not(caption)>tr>th {
        padding: 0.625rem 1.5rem .625rem !important;
        background-color: #6a6e51 !important;
    }

    .table>tr>td {
        padding: 0.625rem 1.5rem .625rem !important;
    }

    .table-bordered> :not(caption)>tr>th,
    .table-bordered> :not(caption)>tr>td {
        border-width: 1px 1px;
        border-color: #6a6e51;
    }

    .table> :not(caption)>tr>td {
        padding: .8rem 1rem !important;
    }

    .wishlist-item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }

    .btn-wishlist-action {
        margin: 2px;
        padding: 5px 10px;
        font-size: 12px;
    }

    .product-name {
        font-weight: 600;
        color: #333;
    }

    .product-price {
        font-weight: 600;
        color: #6a6e51;
    }

    .product-brand {
        color: #666;
        font-size: 0.9em;
    }

    .empty-wishlist {
        text-align: center;
        padding: 3rem 0;
    }

    .empty-wishlist i {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 1rem;
    }

    .wishlist-actions {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
</style>

<main class="pt-90" style="padding-top: 0px;">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">My Wishlist</h2>
        <div class="row">
            <div class="col-lg-2">
                @include('user.account-nav')
            </div>

            <div class="col-lg-10">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($wishlistItems->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>You have {{ $wishlistItems->total() }} item(s) in your wishlist</h5>
                        <form action="{{ route('wishlist.clear') }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to clear your entire wishlist?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fa fa-trash"></i> Clear Wishlist
                            </button>
                        </form>
                    </div>

                    <div class="wg-table table-all-user">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Name</th>
                                        <th class="text-center">Brand</th>
                                        <th class="text-center">Category</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Added On</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wishlistItems as $wishlistItem)
                                        @php
                                            $product = $wishlistItem->product;
                                            $imageUrl = $product->image ? asset('uploads/products/thumbnails/' . $product->image) : asset('images/products/17.png');
                                            $salePrice = $product->sale_price ?: $product->regular_price;
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" 
                                                     class="wishlist-item-image"
                                                     onerror="this.src='/images/products/17.png'">
                                            </td>
                                            <td>
                                                <div class="product-name">{{ $product->name }}</div>
                                                <div class="text-muted" style="font-size: 0.9em;">
                                                </div>
                                            </td>
                                            <td class="text-center product-brand">
                                                {{ $product->brand ? $product->brand->name : 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $product->category ? $product->category->name : 'N/A' }}
                                            </td>
                                            <td class="text-center product-price">
                                                @if($product->sale_price && $product->sale_price < $product->regular_price)
                                                    <span class="text-decoration-line-through text-muted">RM{{ number_format($product->regular_price, 2) }}</span><br>
                                                    <span class="text-danger">RM{{ number_format($product->sale_price, 2) }}</span>
                                                @else
                                                    RM{{ number_format($product->regular_price, 2) }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($product->stock_status === 'instock' && $product->quantity > 0)
                                                    <span class="badge bg-success">In Stock ({{ $product->quantity }})</span>
                                                @else
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $wishlistItem->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="text-center">
                                                <div class="wishlist-actions">
                                                    <a href="{{ route('shop.product.details', $product->slug) }}" 
                                                       class="btn btn-outline-primary btn-wishlist-action" title="View Product">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($product->stock_status === 'instock' && $product->quantity > 0)
                                                        <form action="{{ route('cart.add') }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                                            <input type="hidden" name="name" value="{{ htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') }}">
                                                            <input type="hidden" name="quantity" value="1">
                                                            <input type="hidden" name="price" value="{{ $salePrice }}">
                                                            <button type="submit" class="btn btn-success btn-wishlist-action" title="Add to Cart">
                                                                <i class="fa fa-shopping-cart"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-wishlist-action remove-from-wishlist" 
                                                            data-product-id="{{ $product->id }}" 
                                                            title="Remove from Wishlist">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="divider"></div>
                    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                        {{ $wishlistItems->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="empty-wishlist">
                        <i class="fa fa-heart-o"></i>
                        <h4>Your wishlist is empty</h4>
                        <p>Start adding products you love to your wishlist!</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary">
                            <i class="fa fa-shopping-bag"></i> Continue Shopping
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Remove from wishlist functionality
    $('.remove-from-wishlist').on('click', function() {
        const productId = $(this).data('product-id');
        const row = $(this).closest('tr');
        
        if (confirm('Are you sure you want to remove this item from your wishlist?')) {
            $.ajax({
                url: `/wishlist/remove/${productId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if table is empty
                            if ($('tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                        
                        // Show success message
                        $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                          response.message +
                          '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                          '</div>').prependTo('.col-lg-10').hide().fadeIn();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Error removing item from wishlist');
                }
            });
        }
    });

    // Handle add to cart from wishlist - visual feedback
    $('form[action*="cart/add"]').on('submit', function(e) {
        const form = $(this);
        const button = form.find('button[type="submit"]');
        const row = form.closest('tr');
        
        // Disable button to prevent double submission
        button.prop('disabled', true);
        button.html('<i class="fa fa-spinner fa-spin"></i>');
        

        setTimeout(function() {
            // Re-enable button in case of error
            button.prop('disabled', false);
            button.html('<i class="fa fa-shopping-cart"></i>');
        }, 3000);
    });
});
</script>

@endsection
