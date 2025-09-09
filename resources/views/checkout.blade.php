@extends('layouts.app')
@section('content')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Shipping and Checkout</h2>
        <div class="checkout-steps">
            <a href="{{route('cart.index')}}" class="checkout-steps__item active">
                <span class="checkout-steps__item-number">01</span>
                <span class="checkout-steps__item-title">
                    <span>Shopping Bag</span>
                    <em>Manage Your Items List</em>
                </span>
            </a>
            <a href="javascript:void(0)" class="checkout-steps__item active">
                <span class="checkout-steps__item-number">02</span>
                <span class="checkout-steps__item-title">
                    <span>Shipping and Checkout</span>
                    <em>Checkout Your Items List</em>
                </span>
            </a>
            <a href="javascript:void(0)" class="checkout-steps__item">
                <span class="checkout-steps__item-number">03</span>
                <span class="checkout-steps__item-title">
                    <span>Confirmation</span>
                    <em>Review And Submit Your Order</em>
                </span>
            </a>
        </div>
        @if(session('error'))
        <div class="alert alert-danger mt-3">
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form name="checkout-form" action="{{route('cart.place.order')}}" method="post">
            @csrf
            <div class="checkout-form">
                <div class="billing-info__wrapper">
                    <div class="row">
                        <div class="col-6">
                            <h4>SHIPPING DETAILS</h4>
                        </div>
                        <div class="col-6">
                        </div>
                    </div>

                    @if($address)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="my-account__address-list">
                                <div class="my-account__address-item">
                                    <div class="my-account__address-item__detail">
                                        <div class="row mt-5">
                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="name" required
                                                        value="{{ old('name', $address->name ?? '') }}">
                                                    <label for="name">Full Name *</label>
                                                    @error('name')<span class="text-danger">{{$message}}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="phone" required
                                                        value="{{ old('phone', $address->phone ?? '') }}">
                                                    <label for="phone">Phone Number *</label>
                                                    @error('phone')<span class="text-danger">{{$message}}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="zip" required
                                                        value="{{ old('zip', $address->zip ?? '') }}">
                                                    <label for="zip">Pincode *</label>
                                                    @error('zip')<span class="text-danger">{{$message}}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-floating mt-3 mb-3">
                                                    <input type="text" class="form-control" name="state" required
                                                        value="{{ old('state', $address->state ?? '') }}">
                                                    <label for="state">State *</label>
                                                    @error('state')<span class="text-danger">{{$message}}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="city" required
                                                        value="{{ old('city', $address->city ?? '') }}">
                                                    <label for="city">Town / City *</label>
                                                    @error('city')<span class="text-danger">{{$message}}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="address" required
                                                        value="{{ old('address', $address->address ?? '') }}">
                                                    <label for="address">House no, Building Name *</label>
                                                    @error('address')<span class="text-danger">{{$message}}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="locality" required
                                                        value="{{ old('locality', $address->locality ?? '') }}">
                                                    <label for="locality">Road Name, Area, Colony *</label>
                                                    @error('locality')<span class="text-danger">{{$message}}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-floating my-3">
                                                    <input type="text" class="form-control" name="landmark" required
                                                        value="{{ old('landmark', $address->landmark ?? '') }}">
                                                    <label for="landmark">Landmark *</label>
                                                    @error('landmark')<span class="text-danger">{{$message}}</span>@enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @else
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="name" required="" value="{{old('name')}}">
                                <label for="name">Full Name *</label>
                                @error('name')<span class="text-danger">{{$message}}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="phone" required="" value="{{old('phone')}}">
                                <label for="phone">Phone Number *</label>
                                @error('phone')<span class="text-danger">{{$message}}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="zip" required="" value="{{old('zip')}}">
                                <label for="zip">Pincode *</label>
                                @error('zip')<span class="text-danger">{{$message}}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mt-3 mb-3">
                                <input type="text" class="form-control" name="state" required="" value="{{old('state')}}">
                                <label for="state">State *</label>
                                @error('state')<span class="text-danger">{{$message}}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="city" required="" value="{{old('city')}}">
                                <label for="city">Town / City *</label>
                                @error('city')<span class="text-danger">{{$message}}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="address" required="" value="{{old('address')}}">
                                <label for="address">House no, Building Name *</label>
                                @error('address')<span class="text-danger">{{$message}}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="locality" required="" value="{{old('locality')}}">
                                <label for="locality">Road Name, Area, Colony *</label>
                                @error('locality')<span class="text-danger">{{$message}}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="landmark" required="" value="{{old('landmark')}}">
                                <label for="landmark">Landmark *</label>
                                @error('landmark')<span class="text-danger">{{$message}}</span>@enderror
                            </div>
                        </div>
                    </div>
                    @endif
                </div>


                <div class="checkout__totals-wrapper">
                    <div class="sticky-content">
                        <div class="checkout__totals">
                            <h3>Your Order</h3>
                            <table class="checkout-cart-items">
                                <thead>
                                    <tr>
                                        <th>PRODUCT</th>
                                        <th align="right">SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(Cart::instance('cart')->content() as $item)
                                    <tr>
                                        <td>
                                            {{$item->name}} x {{$item->qty}}
                                        </td>
                                        <td align="right">
                                            RM{{$item->subtotal}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <table class="checkout-totals">
                                <tbody>
                                    <tr>
                                        <th>SUBTOTAL</th>
                                        <td align="right">RM{{Cart::instance('cart')->subtotal()}}</td>
                                    </tr>
                                    <tr>
                                        <th>SHIPPING</th>
                                        <td align="right">Free shipping</td>
                                    </tr>
                                    <tr>
                                        <th>TAX</th>
                                        <td align="right">RM{{Cart::instance('cart')->tax()}}</td>
                                    </tr>
                                    <tr>
                                        <th>TOTAL</th>
                                        <td align="right">RM{{Cart::instance('cart')->total()}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Hidden field for payment method - default to COD -->
                        <input type="hidden" name="mode" value="cod">
                        
                        <button class="btn btn-primary btn-checkout" type="submit" onclick="return validateFormOnClick()">PLACE ORDER</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</main>

<script>
    // Ensure our validation runs after theme.js and overrides any conflicting handlers
    document.addEventListener('DOMContentLoaded', function() {
        // Remove any existing click handlers from theme.js
        $('.checkout-form .btn-checkout').off('click');
    });

    // Fallback validation function for when JavaScript is disabled
    function validateFormOnClick() {
        const requiredFields = ['name', 'phone', 'zip', 'state', 'city', 'address', 'locality', 'landmark'];

        for (const fieldName of requiredFields) {
            const input = document.querySelector(`input[name="${fieldName}"]`);
            if (!input || !input.value || !input.value.trim()) {
                alert(`Please fill in the ${fieldName} field.`);
                if (input) input.focus();
                return false;
            }
        }

        // Validate state format
        const stateInput = document.querySelector('input[name="state"]');
        if (stateInput && stateInput.value) {
            const stateRegex = /^[a-zA-Z\s\-\.]+$/;
            if (!stateRegex.test(stateInput.value)) {
                alert('State must contain only letters, spaces, hyphens, and periods.');
                stateInput.focus();
                return false;
            }
        }

        // Validate city format
        const cityInput = document.querySelector('input[name="city"]');
        if (cityInput && cityInput.value) {
            const cityRegex = /^[a-zA-Z\s\-\.]+$/;
            if (!cityRegex.test(cityInput.value)) {
                alert('City must contain only letters, spaces, hyphens, and periods.');
                cityInput.focus();
                return false;
            }
        }

        // Payment method is now defaulted to COD, no validation needed

        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[name="checkout-form"]');
        const placeOrderBtn = document.querySelector('.btn-checkout');

        // Function to show error message
        function showError(message) {
            // Remove existing error alerts
            const existingAlert = document.querySelector('.checkout-error-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            // Create error alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger checkout-error-alert mt-3';
            alertDiv.innerHTML = `
            <strong>Error:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

            // Insert before the form
            form.parentNode.insertBefore(alertDiv, form);

            // Scroll to error message
            alertDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Function to validate form
        function validateForm() {
            const requiredFields = [{
                    name: 'name',
                    label: 'Full Name'
                },
                {
                    name: 'phone',
                    label: 'Phone Number'
                },
                {
                    name: 'zip',
                    label: 'Pincode'
                },
                {
                    name: 'state',
                    label: 'State'
                },
                {
                    name: 'city',
                    label: 'City'
                },
                {
                    name: 'address',
                    label: 'Address'
                },
                {
                    name: 'locality',
                    label: 'Locality'
                },
                {
                    name: 'landmark',
                    label: 'Landmark'
                }
            ];

            // Check required fields
            for (const field of requiredFields) {
                const input = document.querySelector(`input[name="${field.name}"]`);
                if (!input) {
                    showError(`Form field "${field.name}" not found. Please refresh the page and try again.`);
                    return false;
                }

                if (!input.value || !input.value.trim()) {
                    showError(`Please fill in the ${field.label} field.`);
                    input.focus();
                    input.classList.add('is-invalid');
                    return false;
                } else {
                    input.classList.remove('is-invalid');
                }
            }

            // Validate phone number format
            const phoneInput = document.querySelector('input[name="phone"]');
            if (phoneInput && phoneInput.value) {
                const phoneRegex = /^\d{9,15}$/;
                if (!phoneRegex.test(phoneInput.value)) {
                    showError('Phone number must be between 9 and 15 digits and contain only numbers.');
                    phoneInput.focus();
                    phoneInput.classList.add('is-invalid');
                    return false;
                } else {
                    phoneInput.classList.remove('is-invalid');
                }
            }

            // Validate pincode format
            const zipInput = document.querySelector('input[name="zip"]');
            if (zipInput && zipInput.value) {
                const zipRegex = /^\d{4,10}$/;
                if (!zipRegex.test(zipInput.value)) {
                    showError('Pincode must be between 4 and 10 digits and contain only numbers.');
                    zipInput.focus();
                    zipInput.classList.add('is-invalid');
                    return false;
                } else {
                    zipInput.classList.remove('is-invalid');
                }
            }

            // Validate state format (only letters, spaces, and common punctuation)
            const stateInput = document.querySelector('input[name="state"]');
            if (stateInput && stateInput.value) {
                const stateRegex = /^[a-zA-Z\s\-\.]+$/;
                if (!stateRegex.test(stateInput.value)) {
                    showError('State must contain only letters, spaces, hyphens, and periods.');
                    stateInput.focus();
                    stateInput.classList.add('is-invalid');
                    return false;
                } else {
                    stateInput.classList.remove('is-invalid');
                }
            }

            // Validate city format (only letters, spaces, and common punctuation)
            const cityInput = document.querySelector('input[name="city"]');
            if (cityInput && cityInput.value) {
                const cityRegex = /^[a-zA-Z\s\-\.]+$/;
                if (!cityRegex.test(cityInput.value)) {
                    showError('City must contain only letters, spaces, hyphens, and periods.');
                    cityInput.focus();
                    cityInput.classList.add('is-invalid');
                    return false;
                } else {
                    cityInput.classList.remove('is-invalid');
                }
            }

            // Payment method is now defaulted to COD, no validation needed

            // Check if cart is empty (additional safety check)
            const cartItems = document.querySelectorAll('.checkout-cart-items tbody tr');
            if (cartItems.length === 0) {
                showError('Your cart is empty. Please add items before placing an order.');
                return false;
            }

            return true;
        }

        // Add form submission handler with high priority
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Always prevent default first
            e.stopPropagation(); // Stop event from bubbling up

            // Disable submit button to prevent double submission
            const submitBtn = document.querySelector('.btn-checkout');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';

            if (!validateForm()) {
                // Re-enable button if validation fails
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                return false;
            }

            // If validation passes, submit the form
            this.submit();
        });

        // Also add click handler to prevent other JavaScript from interfering
        const submitBtn = document.querySelector('.btn-checkout');
        submitBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent other click handlers from running
        }, true); // Use capture phase to run before other handlers

        // Add real-time validation for input fields
        const inputs = document.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });

        // Add specific validation for state and city fields
        const stateInput = document.querySelector('input[name="state"]');
        const cityInput = document.querySelector('input[name="city"]');

        if (stateInput) {
            stateInput.addEventListener('input', function() {
                const stateRegex = /^[a-zA-Z\s\-\.]*$/;
                if (this.value && !stateRegex.test(this.value)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }

        if (cityInput) {
            cityInput.addEventListener('input', function() {
                const cityRegex = /^[a-zA-Z\s\-\.]*$/;
                if (this.value && !cityRegex.test(this.value)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }

        // Payment method is now defaulted to COD, no card-related JavaScript needed
    });
</script>

<style>
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .checkout-error-alert {
        border-radius: 0.375rem;
        border: 1px solid #f5c6cb;
        background-color: #f8d7da;
        color: #721c24;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
        opacity: 0.5;
        cursor: pointer;
        float: right;
    }

    .btn-close:hover {
        opacity: 0.75;
    }

</style>

@endsection