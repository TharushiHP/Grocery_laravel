<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes bounce-in {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        @keyframes slide-up {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .animate-bounce-in {
            animation: bounce-in 0.6s ease-out;
        }
        
        .animate-slide-up {
            animation: slide-up 0.8s ease-out;
        }
        
        .success-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 min-h-screen">
    
    <!-- Success Animation Container -->
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            
            <!-- Main Success Card -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-bounce-in">
                
                <!-- Success Icon Header -->
                <div class="success-gradient p-8 text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">Order Placed Successfully!</h1>
                    <p class="text-green-100">Thank you for your purchase</p>
                </div>
                
                <!-- Order Details -->
                <div class="p-8 animate-slide-up">
                    <div class="space-y-4">
                        
                        <!-- Order Number -->
                        <div class="bg-gray-50 rounded-2xl p-4 text-center">
                            <p class="text-sm text-gray-600 mb-1">Order Number</p>
                            <p class="text-2xl font-bold text-gray-800">#{{ $order->id }}</p>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Order Date:</span>
                                <span class="font-semibold">{{ $order->created_at->format('M d, Y') }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Amount:</span>
                                <span class="font-bold text-green-600 text-xl">Rs. {{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            
                            @if($order->customer_name)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Customer:</span>
                                <span class="font-semibold">{{ $order->customer_name }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Delivery Information -->
                        @if($order->delivery_address)
                        <div class="bg-blue-50 rounded-2xl p-4 mt-6">
                            <h3 class="font-semibold text-blue-900 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                Delivery Address
                            </h3>
                            <p class="text-blue-800 text-sm">
                                {{ $order->delivery_address }}<br>
                                {{ $order->delivery_city }} {{ $order->delivery_postal_code }}
                            </p>
                        </div>
                        @endif
                        
                        <!-- Next Steps -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-4 mt-6">
                            <h3 class="font-semibold text-green-900 mb-2">What's Next?</h3>
                            <ul class="text-green-800 text-sm space-y-1">
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Order confirmation sent to your email
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Delivery within 2-3 business days
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Action Button -->
                        <div class="pt-6">
                            <a href="{{ route('home') }}" 
                               class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-2xl text-center block transition-all duration-300 transform hover:scale-105">
                                Continue Shopping
                            </a>
                        </div>
                        
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
    
    <!-- Auto-redirect message (optional) -->
    <script>
        // Show a nice toast message
        setTimeout(() => {
            console.log('Order successfully placed!');
        }, 1000);
        
        // Optional: Auto-redirect after 30 seconds
        // setTimeout(() => {
        //     window.location.href = '{{ route("home") }}';
        // }, 30000);
    </script>
    
</body>
</html>