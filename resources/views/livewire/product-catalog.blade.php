<div>
    <!-- Hero Section - Much Smaller -->
    <section style="position: relative; width: 100%; max-width: 800px; height: 150px; margin: 1rem auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <img src="{{ asset('images/home.jpeg') }}" 
             alt="Scrolling app" 
             style="width: 100%; height: 100%; object-fit: cover;" />
        
        <!-- Text overlay -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
            <div style="text-align: center; color: white; padding: 1rem;">
                <h1 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem; margin-top: 0;">Welcome to Grocery Cart</h1>
                <p style="font-size: 0.875rem; margin: 0;">Scroll, shop, and enjoy your fresh groceries.</p>
            </div>
        </div>
    </section>

    <!-- Search Bar - Inline Styles -->
    <section style="padding: 0 1rem; margin-top: 2rem;">
        <div style="display: flex; justify-content: center;">
            <form style="width: 100%; max-width: 1000px;">
                <input type="text" 
                       wire:model.live="search" 
                       placeholder="Search groceries..." 
                       style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #d1d5db; box-shadow: 0 1px 3px rgba(0,0,0,0.1); font-size: 1rem; outline: none;"
                       onfocus="this.style.boxShadow='0 0 0 2px #16a34a'; this.style.borderColor='#16a34a';"
                       onblur="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.borderColor='#d1d5db';" />
            </form>
        </div>
    </section>

    <!-- Category Filter Section - Inline Styles -->
    <section style="padding: 0 1rem; margin-top: 1.5rem;">
        <div style="display: flex; justify-content: center;">
            <div style="width: 100%; max-width: 400px;">
                <select wire:model.live="selectedCategory" 
                        style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; background: white; outline: none;"
                        onfocus="this.style.boxShadow='0 0 0 2px #16a34a'; this.style.borderColor='#16a34a';"
                        onblur="this.style.boxShadow='none'; this.style.borderColor='#d1d5db';">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="max-w-7xl mx-auto px-4 mt-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('auth_required'))
        <div class="max-w-7xl mx-auto px-4 mt-6">
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('auth_required') }}
                    <button onclick="showAuthModal(); console.log('Direct button clicked');" class="ml-4 bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                        Login Now
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Products Grid -->
    <section class="max-w-7xl mx-auto px-4 mt-8">
        @if($products->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg overflow-hidden transition-shadow duration-300">
                        <!-- Product Image -->
                        <div class="h-48 bg-gray-100 relative">
                            @if($product->image)
                                <img src="{{ asset('images/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gray-200">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
        </div>
    @endif
</div>                        <!-- Product Info -->
                        <div class="p-4">
                            <div class="mb-2">
                                <span class="inline-block bg-green-100 text-green-700 px-2 py-1 rounded text-sm">
                                    {{ ucfirst($product->category) }}
                                </span>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                {{ $product->name }}
                            </h3>
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($product->description, 80) }}</p>
                            
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-xl font-bold text-green-600">
                                        Rs. {{ number_format($product->price, 2) }}
                                    </span>
                                    @if($product->quantity)
                                        <span class="text-sm text-gray-500 block">
                                            per {{ $product->quantity }}
                                        </span>
                                    @endif
                                </div>
                                
                                @auth
                                    <button wire:click="addToCart({{ $product->id }})" 
                                            style="background-color: #16a34a; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; cursor: pointer; display: inline-flex; align-items: center; font-size: 0.875rem; font-weight: 500; transition: background-color 0.3s;"
                                            onmouseover="this.style.backgroundColor='#15803d'"
                                            onmouseout="this.style.backgroundColor='#16a34a'">
                                        <svg style="width: 1rem; height: 1rem; margin-right: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l-2.5 5m0 0h3M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                                        </svg>
                                        Add
                                    </button>
                                @else
                                    <button onclick="showAuthModal()" 
                                            style="background-color: #16a34a; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; cursor: pointer; display: inline-flex; align-items: center; font-size: 0.875rem; font-weight: 500; transition: background-color 0.3s;"
                                            onmouseover="this.style.backgroundColor='#15803d'"
                                            onmouseout="this.style.backgroundColor='#16a34a'">
                                        <svg style="width: 1rem; height: 1rem; margin-right: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l-2.5 5m0 0h3M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                                        </svg>
                                        Add
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $products->links() }}
            </div>
        @else
            <!-- No Products Found -->
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">No products found</h3>
                    <p class="text-gray-500 mb-6">We couldn't find any products matching your search. Try adjusting your filters or search terms.</p>
                    <button wire:click="$set('search', '')" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded transition-colors duration-300">
                        Clear Search
                    </button>
                </div>
            </div>
        @endif
    </section>

    <!-- Authentication Modal -->
    <div id="authModal" style="
        display: none; 
        position: fixed; 
        top: 0; 
        left: 0; 
        width: 100vw; 
        height: 100vh; 
        background-color: rgba(0, 0, 0, 0.6); 
        z-index: 9999;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(2px);
    ">
        <div style="
            background: white; 
            padding: 2rem; 
            border-radius: 12px; 
            width: 90%; 
            max-width: 450px; 
            text-align: center; 
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transform: scale(0.9);
            transition: transform 0.3s ease;
        " id="authModalContent">
            <div style="margin-bottom: 1.5rem;">
                <h3 style="
                    margin-bottom: 0.5rem; 
                    color: #1f2937; 
                    font-size: 1.5rem; 
                    font-weight: 700;
                    line-height: 1.2;
                ">Please Sign In</h3>
                <p style="
                    margin-bottom: 0; 
                    color: #6b7280; 
                    font-size: 1rem;
                    line-height: 1.5;
                ">You need to sign in or register to add products to your cart.</p>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <a href="{{ route('login') }}" 
                   style="
                       flex: 1;
                       background: linear-gradient(135deg, #3b82f6, #2563eb);
                       color: white; 
                       padding: 0.875rem 1.5rem; 
                       border-radius: 8px; 
                       text-decoration: none; 
                       font-weight: 600; 
                       font-size: 1rem;
                       display: flex;
                       align-items: center;
                       justify-content: center;
                       transition: all 0.3s ease;
                       border: none;
                       cursor: pointer;
                   "
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(59, 130, 246, 0.4)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    Sign In
                </a>
                
                <a href="{{ route('register') }}" 
                   style="
                       flex: 1;
                       background: linear-gradient(135deg, #10b981, #059669);
                       color: white; 
                       padding: 0.875rem 1.5rem; 
                       border-radius: 8px; 
                       text-decoration: none; 
                       font-weight: 600; 
                       font-size: 1rem;
                       display: flex;
                       align-items: center;
                       justify-content: center;
                       transition: all 0.3s ease;
                       border: none;
                       cursor: pointer;
                   "
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(16, 185, 129, 0.4)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    Register
                </a>
            </div>
            
            <button onclick="closeAuthModal()" 
                    style="
                        background: transparent; 
                        border: 2px solid #e5e7eb; 
                        color: #6b7280; 
                        padding: 0.75rem 2rem; 
                        border-radius: 8px; 
                        cursor: pointer; 
                        font-size: 0.875rem;
                        font-weight: 500;
                        transition: all 0.3s ease;
                    "
                    onmouseover="this.style.backgroundColor='#f3f4f6'; this.style.borderColor='#d1d5db';"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='#e5e7eb';">
                Cancel
            </button>
        </div>
    </div>

    <script>
        // Simple and reliable modal functions
        function showAuthModal() {
            console.log('Showing auth modal');
            const modal = document.getElementById('authModal');
            const content = document.getElementById('authModalContent');
            
            if (modal && content) {
                modal.style.display = 'flex';
                // Add smooth animation
                setTimeout(() => {
                    content.style.transform = 'scale(1)';
                }, 10);
                console.log('Auth modal shown successfully');
            } else {
                console.error('Modal elements not found');
                // Fallback alert if modal fails
                if (confirm('Please sign in to add items to your cart. Would you like to go to the login page?')) {
                    window.location.href = '{{ route("login") }}';
                }
            }
        }
        
        function closeAuthModal() {
            console.log('Closing auth modal');
            const modal = document.getElementById('authModal');
            const content = document.getElementById('authModalContent');
            
            if (modal && content) {
                content.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 200);
            }
        }
        
        // Set up click outside to close when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Setting up modal event listeners');
            const modal = document.getElementById('authModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeAuthModal();
                    }
                });
                console.log('Modal event listeners set up successfully');
            }
            
            // Test function - you can call this from browser console to test
            window.testAuthModal = showAuthModal;
        });
    </script>
</div>
