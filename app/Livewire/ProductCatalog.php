<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class ProductCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = '';
    public $perPage = 12;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            // Just return early - we'll handle the popup in the frontend
            return;
        }

        $product = Product::findOrFail($productId);
        
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image ?? ''
            ];
        }
        
        session()->put('cart', $cart);
        
        $this->dispatch('cart-updated');
        
        session()->flash('message', $product->name . ' added to cart!');
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, fn($query) => $query->search($this->search))
            ->when($this->selectedCategory, fn($query) => $query->byCategory($this->selectedCategory))
            ->paginate($this->perPage);

        $categories = Product::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('livewire.product-catalog', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
