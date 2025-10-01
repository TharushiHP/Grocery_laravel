<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;

class ShoppingCart extends Component
{
    public $cartItems = [];
    public $isOpen = false;
    
    public function mount()
    {
        $this->loadCart();
    }

    #[On('add-to-cart')]
    public function addToCart($productId, $quantity = 1)
    {
        if (!auth()->check()) {
            session()->flash('error', 'Please login to add items to cart');
            return;
        }

        $product = Product::find($productId);
        
        if (!$product) {
            session()->flash('error', 'Product not found');
            return;
        }

        if ($product->quantity < $quantity) {
            session()->flash('error', 'Insufficient stock available');
            return;
        }

        $existingItemIndex = collect($this->cartItems)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($existingItemIndex !== false) {
            $this->cartItems[$existingItemIndex]['quantity'] = (int)$this->cartItems[$existingItemIndex]['quantity'] + (int)$quantity;
        } else {
            $this->cartItems[] = [
                'product_id' => (int)$product->id,
                'name' => $product->name,
                'price' => (float)$product->price,
                'quantity' => (int)$quantity,
                'image' => $product->image
            ];
        }

        $this->saveCart();
        session()->flash('success', 'Product added to cart!');
        $this->dispatch('cart-updated');
    }

    public function removeItem($index)
    {
        unset($this->cartItems[$index]);
        $this->cartItems = array_values($this->cartItems);
        $this->saveCart();
        $this->dispatch('cart-updated');
    }

    public function updateQuantity($index, $quantity)
    {
        $quantity = (int)$quantity;
        if ($quantity <= 0) {
            $this->removeItem($index);
            return;
        }

        $this->cartItems[$index]['quantity'] = $quantity;
        $this->saveCart();
        $this->dispatch('cart-updated');
    }

    public function clearCart()
    {
        $this->cartItems = [];
        $this->saveCart();
        // Also clear checkout_cart session
        session()->forget(['cart', 'checkout_cart']);
        $this->dispatch('cart-updated');
    }

    public function checkout()
    {
        \Log::info('Checkout method called');
        \Log::info('User authenticated:', ['authenticated' => auth()->check()]);
        \Log::info('Cart items count:', ['count' => count($this->cartItems)]);
        
        if (!auth()->check()) {
            session()->flash('error', 'Please login to checkout');
            \Log::info('Checkout failed: User not authenticated');
            $this->dispatch('checkout-failed', ['reason' => 'not_authenticated']);
            return;
        }

        if (empty($this->cartItems)) {
            session()->flash('error', 'Your cart is empty');
            \Log::info('Checkout failed: Cart is empty');
            $this->dispatch('checkout-failed', ['reason' => 'empty_cart']);
            return;
        }

        \Log::info('Cart items:', $this->cartItems);
        \Log::info('Cart items debug:', [
            'items' => array_map(function($item) {
                return [
                    'product_id' => $item['product_id'] . ' (' . gettype($item['product_id']) . ')',
                    'price' => $item['price'] . ' (' . gettype($item['price']) . ')',
                    'quantity' => $item['quantity'] . ' (' . gettype($item['quantity']) . ')'
                ];
            }, $this->cartItems)
        ]);
        \Log::info('Total amount:', ['total' => $this->getTotal()]);

        // Store cart items in session for checkout form
        session()->put('checkout_cart', $this->cartItems);
        
        \Log::info('Redirecting to checkout form');
        
        // Redirect to checkout form instead of creating order directly
        return $this->redirect(route('checkout'), navigate: true);
    }

    public function getTotal()
    {
        return collect($this->cartItems)->sum(function ($item) {
            $price = is_numeric($item['price']) ? (float)$item['price'] : 0;
            $quantity = is_numeric($item['quantity']) ? (int)$item['quantity'] : 0;
            return $price * $quantity;
        });
    }

    #[Computed]
    public function itemCount()
    {
        return collect($this->cartItems)->sum('quantity');
    }

    #[Computed]
    public function total()
    {
        return collect($this->cartItems)->sum(function ($item) {
            $price = is_numeric($item['price']) ? (float)$item['price'] : 0;
            $quantity = is_numeric($item['quantity']) ? (int)$item['quantity'] : 0;
            return $price * $quantity;
        });
    }

    public function getItemCount()
    {
        return collect($this->cartItems)->sum('quantity');
    }

    public function toggleCart()
    {
        $this->isOpen = !$this->isOpen;
    }

    private function loadCart()
    {
        $this->cartItems = session()->get('cart', []);
    }

    private function saveCart()
    {
        session()->put('cart', $this->cartItems);
    }

    public function render()
    {
        return view('livewire.shopping-cart');
    }
}