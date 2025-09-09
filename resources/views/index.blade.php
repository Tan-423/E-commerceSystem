@extends('layouts.app')
@section('content')

  <main>

    <section class="swiper-container js-swiper-slider swiper-number-pagination slideshow" data-settings='{
        "autoplay": {
          "delay": 5000
        },
        "slidesPerView": 1,
        "effect": "fade",
        "loop": true
      }'>
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <div class="overflow-hidden position-relative h-100">
          <video 
          class="position-absolute top-0 start-0 w-100 h-100" 
          autoplay 
          muted 
          loop 
          playsinline
          style="object-fit: cover; z-index: 1;">
          <source src="{{ asset('video/main.mp4') }}" type="video/mp4">
          Your browser does not support the video tag.
        </video>
          </div>
        </div>
      </div>

      <div class="container">
        <div
          class="slideshow-pagination slideshow-number-pagination d-flex align-items-center position-absolute bottom-0 mb-5">
        </div>
      </div>
    </section>

    <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>

    <!-- Products Grid Section -->
    @if(isset($products) && $products->count() > 0)
    <section class="products-grid container">
      <h2 class="section-title text-center mb-4">Our Latest Products</h2>
      <div class="row">
        @foreach($products->take(4) as $product)
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
          <div class="product-card position-relative">
            <div class="product-card__image position-relative overflow-hidden">
              @if($product->image)
                <img loading="lazy" class="w-100" 
                     src="{{ asset('uploads/products/' . $product->image) }}" 
                     alt="{{ $product->name }}" 
                     style="object-fit: cover; height: 300px;" />
              @else
                <img loading="lazy" class="w-100" 
                     src="{{ asset('assets/images/products/default.png') }}" 
                     alt="{{ $product->name }}" 
                     style="object-fit: cover; height: 300px;" />
              @endif
              
              <!-- Product overlay on hover -->
              <div class="product-card__overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.7); opacity: 0; transition: opacity 0.3s;">
                <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}" 
                   class="btn btn-primary btn-sm">View Details</a>
              </div>
            </div>
            
            <div class="product-card__info p-3 text-center">
              <h6 class="product-card__title mb-2">
                <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}" 
                   class="text-decoration-none text-dark fw-medium">{{ $product->name }}</a>
              </h6>
              
              <div class="product-card__price">
                @if($product->sale_price && $product->sale_price != $product->regular_price)
                  <span class="money price price-sale fw-bold">RM{{ $product->sale_price }}</span>
                  <span class="money price price-old text-muted text-decoration-line-through ms-2">RM{{ $product->regular_price }}</span>
                @else
                  <span class="money price price-sale fw-bold">RM{{ $product->regular_price }}</span>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      
      <div class="text-center mt-4">
        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">View All Products</a>
      </div>
    </section>
    @endif

    <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>

    <style>
    .product-card {
      border: 1px solid #eee;
      border-radius: 8px;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .product-card:hover .product-card__overlay {
      opacity: 1 !important;
    }
    </style>

  </main>

@endsection