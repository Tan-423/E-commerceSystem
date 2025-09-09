@extends('layouts.app')
@section('content')
<style>
  .filled-heart {
    color: red;
  }

  /* Enhanced thumbnail styling */
  .product-single__thumbnail {
    margin-top: 15px;
  }

  .thumbnail-swiper {
    position: relative;
    overflow: hidden;
  }

  .thumbnail-slide {
    width: auto !important;
    margin-right: 10px;
  }

  .thumbnail-wrapper {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
    background: #f8f9fa;
  }

  .thumbnail-wrapper:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-color: #dee2e6;
  }

  .thumbnail-img {
    width: 104px;
    height: 104px;
    object-fit: cover;
    border-radius: 6px;
    transition: all 0.3s ease;
    display: block;
  }

  .thumbnail-img:hover {
    opacity: 0.9;
  }

  .thumbnail-img:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
  }

  /* Selected state */
  .thumbnail-wrapper.selected {
    border-color: #007bff;
    background: #e3f2fd;
    box-shadow: 0 0 0 1px #007bff;
  }

  .thumbnail-wrapper.selected .thumbnail-img {
    opacity: 1;
  }

  /* Navigation buttons */
  .thumbnail-next,
  .thumbnail-prev {
    width: 30px;
    height: 30px;
    margin-top: -15px;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #dee2e6;
    border-radius: 50%;
    color: #6c757d;
    font-size: 12px;
    transition: all 0.3s ease;
  }

  .thumbnail-next:hover,
  .thumbnail-prev:hover {
    background: #007bff;
    color: white;
    border-color: #007bff;
  }

  .thumbnail-next:after,
  .thumbnail-prev:after {
    font-size: 12px;
    font-weight: bold;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .thumbnail-img {
      width: 80px;
      height: 80px;
    }

    .thumbnail-slide {
      margin-right: 8px;
    }

    .thumbnail-next,
    .thumbnail-prev {
      width: 25px;
      height: 25px;
      margin-top: -12.5px;
      font-size: 10px;
    }
  }

  @media (max-width: 480px) {
    .thumbnail-img {
      width: 70px;
      height: 70px;
    }

    .thumbnail-slide {
      margin-right: 6px;
    }
  }
</style>

<div class="mb-3 pb-2 pb-xl-3"></div>
<main class="pt-90">
  <div class="mb-md-1 pb-md-3"></div>
  <section class="product-single container">
    <div class="row">
      <div class="col-lg-7">
        <div class="product-single__media" data-media-type="vertical-thumbnail">
          <div class="product-single__image">
            <div class="swiper-container">

              <div class="swiper-wrapper">
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy" class="h-auto" src="{{asset('uploads/products')}}/{{ $product->image }}"
                    width="674" height="674" alt="" />
                </div>
                @foreach (explode(',', $product->gallery ) as $gimg)
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy" class="h-auto" src="{{asset('uploads/products')}}/{{ $gimg }}" width="674"
                    height="674" alt="" />
                </div>
                @endforeach
              </div>

            </div>
          </div>

          <div class="product-single__thumbnail">
            <div class="swiper-container thumbnail-swiper">
              <div class="swiper-wrapper">
                <!-- Main product image thumbnail -->
                <div class="swiper-slide product-single__image-item thumbnail-slide">
                  <div class="thumbnail-wrapper">
                    <img loading="lazy" class="h-auto thumbnail-img"
                      src="{{asset('uploads/products/thumbnails')}}/{{ $product->image }}"
                      width="104" height="104"
                      alt="Product image thumbnail"
                      data-image="{{ $product->image }}"
                      data-selected="true"
                      role="button"
                      tabindex="0"
                      aria-label="Select main product image" />
                  </div>
                </div>
                <!-- Gallery images thumbnails -->
                @foreach (explode(',', $product->gallery) as $index => $gimg)
                @if(trim($gimg) !== '')
                <div class="swiper-slide product-single__image-item thumbnail-slide">
                  <div class="thumbnail-wrapper">
                    <img loading="lazy" class="h-auto thumbnail-img"
                      src="{{ asset('uploads/products/thumbnails') }}/{{ $gimg }}"
                      width="104" height="104"
                      data-image="{{ $gimg }}"
                      data-selected="false"
                      alt="Product gallery image {{ $index + 1 }}"
                      role="button"
                      tabindex="0"
                      aria-label="Select gallery image {{ $index + 1 }}" />
                  </div>
                </div>
                @endif
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="col-lg-5">
        <div class="d-flex justify-content-between mb-4 pb-md-2">
          <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
            <a href="{{ route('home.index') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
            <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
            <a href="{{ route('shop.index') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">The Shop</a>
          </div><!-- /.breadcrumb -->
        </div>


        <h1 class="product-single__name">{{ $product->name }}</h1>
        <div class="product-single__price">
          <span class="current-price">
            @if($product->sale_price && $product->sale_price != $product->regular_price)
            <s>RM{{$product->regular_price}}</s> RM{{$product->sale_price}}
            @else
            RM{{$product->regular_price}}
            @endif
          </span>
        </div>
        <div class="product-single__short-desc">
          <p>{{ $product->short_description }}</p>
        </div>

        <!-- Selected Image Display -->
        <div class="selected-image-display mb-3">
          <label class="form-label fw-medium">Selected Image:</label>
          <div class="selected-image-preview">
            <img id="selectedImagePreview" src="{{asset('uploads/products/thumbnails')}}/{{ $product->image }}"
              width="80" height="80" alt="Selected Image" class="border rounded" />
            <span id="selectedImageName" class="ms-2 text-muted">{{ $product->image }}</span>
          </div>
        </div>
        <div class="d-flex gap-2 mb-3">
          @auth
          <button type="button"
            class="btn btn-outline-danger wishlist-btn-details"
            data-product-id="{{ $product->id }}"
            title="Add to Wishlist">
            <i class="fa fa-heart"></i> <span class="wishlist-text">Add to Wishlist</span>
          </button>
          @else
          <a href="{{ route('login') }}" class="btn btn-outline-secondary">
            <i class="fa fa-heart"></i> Login to Add to Wishlist
          </a>
          @endauth
        </div>

        <form name="addtocart-form" method="post" action="{{ route('cart.add') }}">

          @csrf
          <div class="product-single__addtocart">
            <div class="qty-control position-relative">
              <input type="number" name="quantity" value="1" min="1" class="qty-control__number text-center">
              <div class="qty-control__reduce">-</div>
              <div class="qty-control__increase">+</div>
            </div><!-- .qty-control -->
            <input type="hidden" name="id" value="{{ $product->id }}" />
            <input type="hidden" name="name" value="{{ $product->name }}" />
            <input type="hidden" name="price" value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price}}" />
            <input type="hidden" name="selected_image" id="selectedImageInput" value="{{ $product->image }}" />
            <button type="submit" class="btn btn-primary btn-addtocart" data-aside="cartDrawer">Add to Cart</button>
          </div>
        </form>



        <div class="product-single__meta-info">
          <div class="meta-item">
            <label>SKU:</label>
            <span>{{ $product->SKU }}</span>
          </div>
          <div class="meta-item">
            <label>Categories:</label>
            <span>{{ $product->category->name }}</span>
          </div>
        </div>
      </div>
    </div>

    </div>
    <div class="product-single__details-tab">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab"
            href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">Description</a>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-description" role="tabpanel"
          aria-labelledby="tab-description-tab">
          <div class="product-single__description">
            {{ $product->description }}
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Get all thumbnail images and wrappers
      const thumbnailImages = document.querySelectorAll('.thumbnail-img');
      const thumbnailWrappers = document.querySelectorAll('.thumbnail-wrapper');
      const selectedImagePreview = document.getElementById('selectedImagePreview');
      const selectedImageName = document.getElementById('selectedImageName');
      const selectedImageInput = document.getElementById('selectedImageInput');

      // Function to update selected thumbnail
      function selectThumbnail(clickedImg, clickedWrapper) {
        // Remove selected class from all wrappers
        thumbnailWrappers.forEach(function(wrapper) {
          wrapper.classList.remove('selected');
        });

        // Add selected class to clicked wrapper
        clickedWrapper.classList.add('selected');

        // Update the selected image preview
        const selectedImage = clickedImg.getAttribute('data-image');
        selectedImagePreview.src = "{{ asset('uploads/products/thumbnails') }}/" + selectedImage;
        selectedImageName.textContent = selectedImage;
        selectedImageInput.value = selectedImage;

        // Update main image display
        const mainImage = document.querySelector('.product-single__image img');
        if (mainImage) {
          mainImage.src = "{{ asset('uploads/products') }}/" + selectedImage;
        }
      }

      // Add click and keyboard event listeners to all thumbnail images
      thumbnailImages.forEach(function(img, index) {
        const wrapper = img.closest('.thumbnail-wrapper');

        // Click event
        img.addEventListener('click', function() {
          selectThumbnail(this, wrapper);
        });

        // Keyboard events for accessibility
        img.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            selectThumbnail(this, wrapper);
          }
        });

        // Arrow key navigation
        img.addEventListener('keydown', function(e) {
          if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
            e.preventDefault();
            let nextIndex;

            if (e.key === 'ArrowRight') {
              nextIndex = (index + 1) % thumbnailImages.length;
            } else {
              nextIndex = (index - 1 + thumbnailImages.length) % thumbnailImages.length;
            }

            thumbnailImages[nextIndex].focus();
          }
        });
      });

      // Set initial selected state for the first image
      if (thumbnailWrappers.length > 0) {
        thumbnailWrappers[0].classList.add('selected');
      }

      // Initialize Swiper for thumbnails if more than 4 images
      if (thumbnailImages.length > 4) {
        const thumbnailSwiper = new Swiper('.thumbnail-swiper', {
          slidesPerView: 'auto',
          spaceBetween: 10,
          freeMode: true,
          navigation: {
            nextEl: '.thumbnail-next',
            prevEl: '.thumbnail-prev',
          },
          breakpoints: {
            320: {
              slidesPerView: 3,
              spaceBetween: 6
            },
            480: {
              slidesPerView: 4,
              spaceBetween: 8
            },
            768: {
              slidesPerView: 5,
              spaceBetween: 10
            },
            1024: {
              slidesPerView: 6,
              spaceBetween: 10
            }
          }
        });
      }
    });
  </script>

  @auth
  <script>
    // Ensure jQuery is loaded before running wishlist functionality
    document.addEventListener('DOMContentLoaded', function() {
      // Wait for jQuery to be available
      function waitForJQuery(callback) {
        if (typeof $ !== 'undefined') {
          callback();
        } else {
          setTimeout(function() {
            waitForJQuery(callback);
          }, 100);
        }
      }

      waitForJQuery(function() {
        // Wishlist functionality for product details page
        $(document).ready(function() {
          const productId = '{{ $product->id }}';
          const wishlistBtn = $('.wishlist-btn-details');

          // Check if product is in wishlist on page load
          $.get('/wishlist/check/' + productId)
            .done(function(response) {
              console.log('Wishlist check response:', response);
              if (response.inWishlist) {
                wishlistBtn.removeClass('btn-outline-danger').addClass('btn-danger');
                wishlistBtn.find('.wishlist-text').text('Remove from Wishlist');
                wishlistBtn.attr('title', 'Remove from Wishlist');
                wishlistBtn.addClass('in-wishlist');
              }
            })
            .fail(function(xhr, status, error) {
              console.log('Error checking wishlist status:', error);
              console.log('XHR:', xhr);
            });

          // Handle wishlist button click
          wishlistBtn.on('click', function(e) {
            e.preventDefault();
            console.log('Wishlist button clicked');

            const isInWishlist = $(this).hasClass('in-wishlist');
            console.log('Is in wishlist:', isInWishlist);

            // Disable button during request
            $(this).prop('disabled', true);

            if (isInWishlist) {
              // Remove from wishlist
              $.ajax({
                url: '/wishlist/remove/' + productId,
                type: 'DELETE',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                  console.log('Remove response:', response);
                  if (response.success) {
                    wishlistBtn.removeClass('btn-danger in-wishlist').addClass('btn-outline-danger');
                    wishlistBtn.find('.wishlist-text').text('Add to Wishlist');
                    wishlistBtn.attr('title', 'Add to Wishlist');

                    showToast('Product removed from wishlist!', 'success');

                    // Trigger counter update
                    $(document).trigger('wishlistUpdated');
                  }
                },
                error: function(xhr, status, error) {
                  console.log('Error removing from wishlist:', error);
                  console.log('XHR:', xhr);
                  const response = xhr.responseJSON;
                  showToast(response?.message || 'Error removing from wishlist', 'error');
                },
                complete: function() {
                  wishlistBtn.prop('disabled', false);
                }
              });
            } else {
              // Add to wishlist
              $.ajax({
                url: '/wishlist/add',
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                  product_id: productId
                },
                success: function(response) {
                  console.log('Add response:', response);
                  if (response.success) {
                    wishlistBtn.removeClass('btn-outline-danger').addClass('btn-danger in-wishlist');
                    wishlistBtn.find('.wishlist-text').text('Remove from Wishlist');
                    wishlistBtn.attr('title', 'Remove from Wishlist');

                    showToast('Product added to wishlist!', 'success');

                    // Trigger counter update
                    $(document).trigger('wishlistUpdated');
                  }
                },
                error: function(xhr, status, error) {
                  console.log('Error adding to wishlist:', error);
                  console.log('XHR:', xhr);
                  const response = xhr.responseJSON;
                  showToast(response?.message || 'Error adding to wishlist', 'error');
                },
                complete: function() {
                  wishlistBtn.prop('disabled', false);
                }
              });
            }
          });

          // Toast notification function
          function showToast(message, type) {
            const toastClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const toast = $('<div class="alert ' + toastClass + ' alert-dismissible fade show position-fixed" style="top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px;" role="alert">' +
              message +
              '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
              '</div>');

            $('body').append(toast);

            setTimeout(function() {
              toast.alert('close');
            }, 3000);
          }
        });
      });
    });
  </script>
  @endauth
  @endsection