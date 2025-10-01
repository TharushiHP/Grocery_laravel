<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;

class CheckoutController extends Controller
{
    public function show()
    {
        // Check for cart items from Livewire component (checkout_cart) or legacy cart
        $cartItems = session('checkout_cart', session('cart', []));
        $total = 0;
        
        // If no cart items, redirect back to home
        if (empty($cartItems)) {
            return redirect()->route('home')->with('error', 'Your cart is empty.');
        }
        
        // Calculate total and get product details
        foreach ($cartItems as $productId => $item) {
            if (is_array($item) && isset($item['price'], $item['quantity'])) {
                $price = is_numeric($item['price']) ? (float)$item['price'] : 0;
                $quantity = is_numeric($item['quantity']) ? (int)$item['quantity'] : 0;
                $total += $price * $quantity;
            }
        }

        return view('checkout', compact('cartItems', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);

        // Get cart items from checkout_cart or legacy cart
        $cartItems = session('checkout_cart', session('cart', []));
        
        if (empty($cartItems)) {
            return redirect()->route('home')->with('error', 'Your cart is empty.');
        }

        // Calculate total with numeric conversion
        $total = 0;
        foreach ($cartItems as $productId => $item) {
            if (is_array($item) && isset($item['price'], $item['quantity'])) {
                $price = is_numeric($item['price']) ? (float)$item['price'] : 0;
                $quantity = is_numeric($item['quantity']) ? (int)$item['quantity'] : 0;
                $total += $price * $quantity;
            }
        }

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => (float)$total,
            'status' => 'pending',
            'delivery_address' => $request->address,
            'delivery_city' => $request->city,
            'delivery_postal_code' => $request->postal_code,
            'customer_name' => $request->name,
            'customer_phone' => $request->phone,
        ]);

        // Create order details with stock updates
        foreach ($cartItems as $productId => $item) {
            if (is_array($item) && isset($item['quantity'], $item['price'])) {
                // Handle both indexed arrays (from Livewire) and associative arrays (legacy)
                $product_id = isset($item['product_id']) ? (int)$item['product_id'] : (int)$productId;
                $quantity = is_numeric($item['quantity']) ? (int)$item['quantity'] : 0;
                $price = is_numeric($item['price']) ? (float)$item['price'] : 0;
                
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
                
                // Update product stock
                $product = Product::find($product_id);
                if ($product) {
                    $product->quantity = (int)$product->quantity - $quantity;
                    $product->save();
                }
            }
        }

        // Clear both cart sessions
        session()->forget(['cart', 'checkout_cart']);

        // Redirect to order success page
        return redirect()->route('order.success', ['order' => $order->id])
                        ->with('success', 'Order placed successfully! Order ID: ' . $order->id);
    }

    /**
     * Show order success page
     */
    public function success(Order $order)
    {
        // Ensure user can only see their own orders
        if (auth()->check() && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        return view('order-success', compact('order'));
    }
}
