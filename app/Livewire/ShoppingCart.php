<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Product;

class ShoppingCart extends Component
{
    public $cartItems = [];
    public $isOpen = false;

    public function mount()
    {
        $this->refreshCart();
    }

    #[On('add-to-cart')]
    public function addToCart($productId)
    {
        // Double-check authentication
        if (!auth()->check()) {
            return;
        }

        $product = Product::find($productId);
        
        if (!$product) {
            return;
        }

        $cartItems = session()->get('cart', []);
        
        if (isset($cartItems[$productId])) {
            $cartItems[$productId]['quantity']++;
        } else {
            $cartItems[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image ?? '',
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cartItems);
        $this->cartItems = $cartItems;
        
        $this->dispatch('cart-updated');
    }

    #[On('cart-updated')]
    public function refreshCart()
    {
        if (auth()->check()) {
            $this->cartItems = session()->get('cart', []);
        } else {
            $this->cartItems = [];
        }
    }

    public function removeFromCart($productId)
    {
        $cartItems = session()->get('cart', []);
        unset($cartItems[$productId]);
        session()->put('cart', $cartItems);
        $this->cartItems = $cartItems;
        
        $this->dispatch('cart-updated');
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $cartItems = session()->get('cart', []);
        if (isset($cartItems[$productId])) {
            $cartItems[$productId]['quantity'] = $quantity;
            session()->put('cart', $cartItems);
            $this->cartItems = $cartItems;
            
            $this->dispatch('cart-updated');
        }
    }

    public function toggleCart()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function getTotal()
    {
        return collect($this->cartItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function getItemCount()
    {
        return collect($this->cartItems)->sum('quantity');
    }

    public function clearCart()
    {
        session()->forget('cart');
        $this->cartItems = [];
        
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.shopping-cart', [
            'total' => $this->getTotal(),
            'itemCount' => $this->getItemCount(),
        ]);
    }
}
