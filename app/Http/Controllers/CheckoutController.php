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
        $cartItems = session('cart', []);
        $total = 0;
        
        // Calculate total and get product details
        foreach ($cartItems as $productId => $item) {
            if (is_array($item) && isset($item['price'], $item['quantity'])) {
                $total += $item['price'] * $item['quantity'];
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

        $cartItems = session('cart', []);
        
        if (empty($cartItems)) {
            return redirect()->route('home')->with('error', 'Your cart is empty.');
        }

        // Calculate total
        $total = 0;
        foreach ($cartItems as $productId => $item) {
            if (is_array($item) && isset($item['price'], $item['quantity'])) {
                $total += $item['price'] * $item['quantity'];
            }
        }

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $total,
            'status' => 'pending',
            'delivery_address' => $request->address,
            'delivery_city' => $request->city,
            'delivery_postal_code' => $request->postal_code,
            'customer_name' => $request->name,
            'customer_phone' => $request->phone,
        ]);

        // Create order details
        foreach ($cartItems as $productId => $item) {
            if (is_array($item) && isset($item['quantity'], $item['price'])) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        }

        // Clear cart
        session()->forget('cart');

        return redirect()->route('home')->with('success', 'Order placed successfully! Order ID: ' . $order->id);
    }
}
