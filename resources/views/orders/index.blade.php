<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order History') }}
        </h2>
    </x-slot>

<div style="min-height: 100vh; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 2rem 1rem;">\n    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Header -->
        <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 2rem; padding: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <a href="{{ route('profile.show') }}" style="color: #6b7280; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Profile
                </a>
            </div>
            <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                <svg style="width: 2rem; height: 2rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Order History
            </h1>
            <p style="color: #6b7280; margin: 0.5rem 0 0; font-size: 1.125rem;">View all your orders and track their status</p>
        </div>

        @php
            $allOrders = Auth::user()->orders()->latest()->paginate(10);
        @endphp

        @if($allOrders->count() > 0)
            <!-- Orders Grid -->
            <div style="display: grid; gap: 1.5rem;">
                @foreach($allOrders as $order)
                    <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">
                        <!-- Order Header -->
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                            <div>
                                <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0;">Order #{{ $order->id }}</h3>
                                <p style="color: #6b7280; margin: 0.25rem 0 0; font-size: 0.875rem;">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0;">Rs. {{ number_format($order->total_amount, 2) }}</p>
                                @if($order->status === 'pending')
                                    <span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background: #fef3c7; color: #92400e; margin-top: 0.5rem;">
                                        Pending
                                    </span>
                                @elseif($order->status === 'processing')
                                    <span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background: #dbeafe; color: #1e40af; margin-top: 0.5rem;">
                                        Processing
                                    </span>
                                @elseif($order->status === 'delivered')
                                    <span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background: #dcfce7; color: #16a34a; margin-top: 0.5rem;">
                                        Delivered
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background: #f3f4f6; color: #374151; margin-top: 0.5rem;">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Order Details -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1rem;">
                            <!-- Customer Info -->
                            <div>
                                <h4 style="font-size: 0.875rem; font-weight: 600; color: #374151; margin: 0 0 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Customer</h4>
                                <p style="color: #1f2937; margin: 0; line-height: 1.5;">
                                    <strong>{{ $order->customer_name ?: Auth::user()->name }}</strong><br>
                                    @if($order->customer_phone)
                                        {{ $order->customer_phone }}<br>
                                    @endif
                                </p>
                            </div>

                            <!-- Delivery Address -->
                            <div>
                                <h4 style="font-size: 0.875rem; font-weight: 600; color: #374151; margin: 0 0 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Delivery Address</h4>
                                <p style="color: #1f2937; margin: 0; line-height: 1.5;">
                                    @if($order->delivery_address && $order->delivery_city)
                                        {{ $order->delivery_address }}<br>
                                        {{ $order->delivery_city }}
                                        @if($order->delivery_postal_code)
                                            , {{ $order->delivery_postal_code }}
                                        @endif
                                    @else
                                        <span style="color: #6b7280;">Address not provided</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div>
                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #374151; margin: 0 0 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                Items ({{ $order->orderDetails->count() }})
                            </h4>
                            <div style="background: #f9fafb; border-radius: 0.375rem; padding: 1rem;">
                                <div style="display: grid; gap: 0.75rem;">
                                    @foreach($order->orderDetails as $detail)
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                @if($detail->product && $detail->product->image_url)
                                                    <img src="{{ $detail->product->image_url }}" alt="{{ $detail->product->name }}" style="width: 2.5rem; height: 2.5rem; border-radius: 0.375rem; object-fit: cover;">
                                                @else
                                                    <div style="width: 2.5rem; height: 2.5rem; background: #e5e7eb; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center;">
                                                        <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p style="font-weight: 500; color: #1f2937; margin: 0;">{{ $detail->product ? $detail->product->name : 'Product not found' }}</p>
                                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Qty: {{ $detail->quantity }}</p>
                                                </div>
                                            </div>
                                            <p style="font-weight: 500; color: #1f2937; margin: 0;">Rs. {{ number_format($detail->price * $detail->quantity, 2) }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $allOrders->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 3rem; text-align: center;">
                <svg style="width: 4rem; height: 4rem; margin: 0 auto 1rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l-2.5 5m0 0h3M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                </svg>
                <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin: 0 0 0.5rem;">No orders yet</h2>
                <p style="color: #6b7280; margin: 0 0 1.5rem;">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                <a href="{{ route('home') }}" style="display: inline-block; background: #16a34a; color: white; padding: 0.75rem 1.5rem; border-radius: 0.375rem; font-weight: 500; text-decoration: none; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
</div>
</x-app-layout>