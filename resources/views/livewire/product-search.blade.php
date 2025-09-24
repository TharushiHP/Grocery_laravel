<div class="relative">
    <div class="relative">
        <input type="text" 
               wire:model.live="search" 
               wire:focus="showResults = true"
               wire:blur="hideResults"
               placeholder="Search products..." 
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    @if($showResults && !empty($results))
        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-y-auto">
            @foreach($results as $product)
                <div wire:click="selectProduct({{ $product->id }})" 
                     class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                            @if($product->image)
                                <img src="{{ asset('images/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover rounded">
                            @else
                                <span class="text-xs text-gray-400">No Image</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-800">{{ $product->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $product->category }} - Rs. {{ number_format($product->price, 2) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($showResults && empty($results) && strlen($search) >= 2)
        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg">
            <div class="px-4 py-3 text-center text-gray-500">
                No products found for "{{ $search }}"
            </div>
        </div>
    @endif
</div>
