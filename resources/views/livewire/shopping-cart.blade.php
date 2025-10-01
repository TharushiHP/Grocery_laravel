<div>
    <!-- Cart Toggle Button - Forced White Styling -->
    <div style="position: relative; display: inline-block;">
        <button wire:click="toggleCart" 
                style="color: white; background: none; border: none; font-size: 0.875rem; font-weight: 500; cursor: pointer; padding: 0.25rem 0.5rem; font-family: inherit; display: inline-flex; align-items: center; gap: 0.25rem;"
                onmouseover="this.style.color='#bbf7d0'; this.style.textDecoration='underline';"
                onmouseout="this.style.color='white'; this.style.textDecoration='none';">
            Cart
            @if($this->itemCount > 0)
                <span style="margin-left: 0.25rem; background-color: #ef4444; color: white; font-size: 0.75rem; font-weight: bold; border-radius: 50%; height: 16px; width: 16px; display: inline-flex; align-items: center; justify-content: center;">
                    {{ $this->itemCount }}
                </span>
            @endif
        </button>

        <!-- Cart Dropdown -->
        @if($isOpen)
            <div class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border z-50 overflow-hidden">
                <div class="bg-green-600 p-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span>Shopping Cart</span>
                        </h3>
                        <button wire:click="toggleCart" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    @if(empty($cartItems))
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">Your cart is empty</p>
                            <p class="text-gray-400 text-sm mt-1">Add some items to get started!</p>
                        </div>
                    @else
                        @foreach($cartItems as $index => $item)
                            <div style="padding: 1rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 1rem; transition: background-color 0.3s;"
                                 onmouseover="this.style.backgroundColor='#f9fafb'"
                                 onmouseout="this.style.backgroundColor='white'">
                                <!-- Product Image -->
                                <div style="width: 3rem; height: 3rem; background-color: #f3f4f6; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                    @if(isset($item['image']) && $item['image'])
                                        <img src="{{ asset('images/' . $item['image']) }}" 
                                             alt="{{ $item['name'] }}" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <svg style="width: 1.5rem; height: 1.5rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div style="flex: 1;">
                                    <h4 style="font-size: 0.875rem; font-weight: 600; color: #1f2937;">{{ $item['name'] }}</h4>
                                    <p style="font-size: 0.875rem; font-weight: 600; color: #16a34a;">
                                        Rs. {{ number_format($item['price'], 2) }}
                                    </p>
                                </div>

                                <!-- Quantity Controls -->
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})" 
                                            style="width: 1.5rem; height: 1.5rem; background-color: #ef4444; color: white; border: none; border-radius: 0.25rem; font-size: 0.875rem; font-weight: bold; cursor: pointer; transition: background-color 0.3s;"
                                            onmouseover="this.style.backgroundColor='#dc2626'"
                                            onmouseout="this.style.backgroundColor='#ef4444'">-</button>
                                    <span style="font-size: 0.875rem; font-weight: 500; width: 2rem; text-align: center; color: #1f2937;">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})" 
                                            style="width: 1.5rem; height: 1.5rem; background-color: #10b981; color: white; border: none; border-radius: 0.25rem; font-size: 0.875rem; font-weight: bold; cursor: pointer; transition: background-color 0.3s;"
                                            onmouseover="this.style.backgroundColor='#059669'"
                                            onmouseout="this.style.backgroundColor='#10b981'">+</button>
                                </div>

                                <!-- Remove Button -->
                                <button wire:click="removeItem({{ $index }})" 
                                        style="color: #ef4444; padding: 0.25rem; border: none; background: none; border-radius: 0.25rem; cursor: pointer; transition: color 0.3s;"
                                        onmouseover="this.style.color='#b91c1c'"
                                        onmouseout="this.style.color='#ef4444'">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>

                @if(!empty($cartItems))
                    <div style="background-color: #f9fafb; padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.125rem; font-weight: 600; color: #1f2937;">Total:</span>
                            <span style="font-size: 1.25rem; font-weight: bold; color: #16a34a;">
                                Rs. {{ number_format($this->total, 2) }}
                            </span>
                        </div>
                        
                        <div style="display: flex; gap: 0.5rem;">
                            <button wire:click="clearCart" 
                                    style="flex: 1; background-color: #6b7280; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; border: none; cursor: pointer; transition: background-color 0.3s;"
                                    onmouseover="this.style.backgroundColor='#4b5563'"
                                    onmouseout="this.style.backgroundColor='#6b7280'">
                                Clear Cart
                            </button>
                            <button wire:click="checkout" 
                                    onclick="console.log('Checkout button clicked')"
                                    style="flex: 1; background-color: #16a34a; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; border: none; cursor: pointer; transition: background-color 0.3s;"
                                    onmouseover="this.style.backgroundColor='#15803d'"
                                    onmouseout="this.style.backgroundColor='#16a34a'">
                                Checkout
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('checkout-failed', (event) => {
        console.log('Checkout failed:', event);
        const reason = event[0].reason;
        const message = event[0].message || '';
        alert(`Checkout failed: ${reason}. ${message}`);
    });
});
</script>
