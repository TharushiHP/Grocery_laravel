<x-layouts.app title="Checkout - Grocery Cart">
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Enhanced Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-4">
                        Complete Your Order
                    </h1>
                    <p class="text-gray-600 text-lg">Review your items and provide delivery details</p>
                </div>
                
                @if($cartItems && count($cartItems) > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Enhanced Order Summary -->
                        <div class="lg:order-2">
                            <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/20 p-8 sticky top-8">
                                <h2 style="font-size: 1.5rem; font-weight: bold; color: #1f2937; margin-bottom: 1.5rem; display: flex; align-items: center;">
                                    <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.75rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Order Summary
                                </h2>
                                
                                <div class="space-y-4 max-h-64 overflow-y-auto">
                                    @foreach($cartItems as $productId => $item)
                                        @if(is_array($item) && isset($item['name'], $item['price'], $item['quantity']))
                                            @php 
                                                $price = is_numeric($item['price']) ? (float)$item['price'] : 0;
                                                $quantity = is_numeric($item['quantity']) ? (int)$item['quantity'] : 0;
                                                $subtotal = $price * $quantity;
                                            @endphp
                                            <div class="flex items-center space-x-4 p-4 bg-gray-50/80 rounded-2xl">
                                                <!-- Product Image -->
                                                <div style="width: 3rem; height: 3rem; background: linear-gradient(to bottom right, #f3f4f6, #e5e7eb); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                                    @if(isset($item['image']) && $item['image'])
                                                        <img src="{{ asset('images/' . $item['image']) }}" 
                                                             alt="{{ $item['name'] }}" 
                                                             style="width: 100%; height: 100%; object-fit: cover;">
                                                    @else
                                                        <svg style="width: 1.5rem; height: 1.5rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                
                                                <!-- Product Details -->
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-800">{{ $item['name'] }}</h4>
                                                    <p class="text-sm text-gray-600">Qty: {{ $item['quantity'] }} × Rs. {{ number_format($item['price'], 2) }}</p>
                                                </div>
                                                
                                                <!-- Price -->
                                                <div class="text-right">
                                                    <p class="font-bold text-green-600">Rs. {{ number_format($subtotal, 2) }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="border-t border-gray-200 pt-6 mt-6">
                                    <div class="flex justify-between items-center">
                                        <span class="text-2xl font-bold text-gray-800">Total:</span>
                                        <span class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                            Rs. {{ number_format($total, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Delivery Form -->
                        <div class="lg:order-1">
                            <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/20 p-8">
                                <h2 style="font-size: 1.5rem; font-weight: bold; color: #1f2937; margin-bottom: 1.5rem; display: flex; align-items: center;">
                                    <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.75rem; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Delivery Information
                                </h2>
                                
                                @if($errors->any())
                                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                                        <div class="flex items-center mb-2">
                                            <svg style="width: 1rem; height: 1rem; color: #ef4444; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <h3 class="text-red-800 font-semibold">Please fix the following errors:</h3>
                                        </div>
                                        <ul class="text-red-700 space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li class="flex items-center">
                                                    <span class="w-1 h-1 bg-red-500 rounded-full mr-2"></span>
                                                    {{ $error }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <form action="{{ route('process.checkout') }}" method="POST" style="display: block; width: 100%;">
                                    @csrf
                                    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                                        <div class="group">
                                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                                <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                Full Name
                                            </label>
                                            <input type="text" 
                                                   name="name" 
                                                   value="{{ old('name', auth()->user()->name) }}" 
                                                   required
                                                   class="w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 backdrop-blur focus:ring-2 focus:ring-green-500 focus:bg-white/90 transition-all duration-300 group-hover:bg-white/60">
                                        </div>

                                        <div class="group">
                                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                                <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                                Email Address
                                            </label>
                                            <input type="email" 
                                                   name="email" 
                                                   value="{{ old('email', auth()->user()->email) }}" 
                                                   required
                                                   class="w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 backdrop-blur focus:ring-2 focus:ring-green-500 focus:bg-white/90 transition-all duration-300 group-hover:bg-white/60">
                                        </div>
                                    </div>

                                    <div class="group mb-6">
                                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Delivery Address
                                        </label>
                                        <textarea name="address" 
                                                  required 
                                                  rows="4"
                                                  placeholder="Enter your complete delivery address..."
                                                  class="w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 backdrop-blur focus:ring-2 focus:ring-green-500 focus:bg-white/90 transition-all duration-300 group-hover:bg-white/60 resize-none">{{ old('address') }}</textarea>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                        <div class="group">
                                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                                <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                Phone Number
                                            </label>
                                            <input type="tel" 
                                                   name="phone" 
                                                   value="{{ old('phone') }}" 
                                                   required
                                                   placeholder="Enter your phone number"
                                                   class="w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 backdrop-blur focus:ring-2 focus:ring-green-500 focus:bg-white/90 transition-all duration-300 group-hover:bg-white/60">
                                        </div>

                                        <div class="group">
                                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                                <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                City
                                            </label>
                                            <input type="text" 
                                                   name="city" 
                                                   value="{{ old('city') }}" 
                                                   required
                                                   placeholder="Enter your city"
                                                   class="w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 backdrop-blur focus:ring-2 focus:ring-green-500 focus:bg-white/90 transition-all duration-300 group-hover:bg-white/60">
                                        </div>

                                        <div class="group">
                                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                                <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h6m-6 4h6m-2 8h.01"></path>
                                                </svg>
                                                Postal Code
                                            </label>
                                            <input type="text" 
                                                   name="postal_code" 
                                                   value="{{ old('postal_code') }}" 
                                                   required
                                                   placeholder="Enter postal code"
                                                   class="w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 backdrop-blur focus:ring-2 focus:ring-green-500 focus:bg-white/90 transition-all duration-300 group-hover:bg-white/60">
                                        </div>
                                    </div>
                                    
                                    <div style="padding-top: 1.5rem; margin-top: 1rem; border-top: 2px solid #16a34a;">
                                        <button type="submit" style="
                                            display: block;
                                            width: 100%;
                                            height: 50px;
                                            background-color: #16a34a;
                                            color: white;
                                            font-size: 16px;
                                            font-weight: bold;
                                            border: 2px solid #15803d;
                                            border-radius: 8px;
                                            cursor: pointer;
                                            text-align: center;
                                            line-height: 46px;
                                            margin: 0;
                                        " 
                                        onmouseover="this.style.backgroundColor='#15803d'"
                                        onmouseout="this.style.backgroundColor='#16a34a'">
                                            Place Order • Rs. {{ number_format(array_sum(array_map(function($item) { 
                                                return is_array($item) && isset($item['price'], $item['quantity']) ? $item['price'] * $item['quantity'] : 0; 
                                            }, session('cart', []))), 2) }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 5rem 0;">
                        <div style="max-width: 28rem; margin: 0 auto;">
                            <div style="width: 4rem; height: 4rem; background: linear-gradient(to bottom right, #f3f4f6, #e5e7eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                                <svg style="width: 2rem; height: 2rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <h3 style="font-size: 1.875rem; font-weight: bold; color: #374151; margin-bottom: 1rem;">Your cart is empty</h3>
                            <p style="color: #6b7280; font-size: 1.125rem; margin-bottom: 2rem;">Add some fresh items to continue with checkout</p>
                            <a href="{{ route('home') }}" 
                               style="display: inline-block; background: linear-gradient(to right, #10b981, #059669); color: white; padding: 1rem 2rem; border-radius: 1rem; font-weight: 600; font-size: 1.125rem; text-decoration: none; box-shadow: 0 10px 15px rgba(0,0,0,0.1); transition: all 0.3s;"
                               onmouseover="this.style.background='linear-gradient(to right, #059669, #047857)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 25px rgba(0,0,0,0.15)';"
                               onmouseout="this.style.background='linear-gradient(to right, #10b981, #059669)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 15px rgba(0,0,0,0.1)';">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Success JavaScript -->
    <script src="{{ asset('js/order-success.js') }}"></script>
</x-layouts.app>