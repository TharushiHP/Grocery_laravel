<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CartController extends ApiController
{
    /**
     * Get the authenticated user's cart.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $cartKey = "cart_user_{$user->id}";
            
            $cart = Cache::get($cartKey, []);
            
            $total = 0;
            $itemCount = 0;
            $cartItems = [];

            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if ($product) {
                    $cartItems[] = [
                        'id' => $productId,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $product->price * $item['quantity'],
                        'image' => $product->image,
                        'category' => $product->category
                    ];
                    $total += $product->price * $item['quantity'];
                    $itemCount += $item['quantity'];
                }
            }

            return $this->successResponse([
                'items' => $cartItems,
                'total' => $total,
                'item_count' => $itemCount,
                'currency' => 'LKR'
            ], 'Cart retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve cart', 500);
        }
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'integer|min:1|max:10'
            ]);

            $user = $request->user();
            $productId = $validated['product_id'];
            $quantity = $validated['quantity'] ?? 1;

            $product = Product::findOrFail($productId);

            $cartKey = "cart_user_{$user->id}";
            $cart = Cache::get($cartKey, []);

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $quantity;
            } else {
                $cart[$productId] = [
                    'quantity' => $quantity,
                    'added_at' => now()->toDateTimeString()
                ];
            }

            // Cache for 24 hours
            Cache::put($cartKey, $cart, now()->addHours(24));

            return $this->successResponse([
                'product' => $product,
                'quantity' => $cart[$productId]['quantity'],
                'message' => "{$product->name} added to cart"
            ], 'Item added to cart successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Product not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to add item to cart', 500);
        }
    }

    /**
     * Update item quantity in cart.
     */
    public function update(Request $request, $productId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:0|max:10'
            ]);

            $user = $request->user();
            $quantity = $validated['quantity'];
            $cartKey = "cart_user_{$user->id}";
            $cart = Cache::get($cartKey, []);

            if (!isset($cart[$productId])) {
                return $this->errorResponse('Item not found in cart', 404);
            }

            if ($quantity == 0) {
                unset($cart[$productId]);
                $message = 'Item removed from cart';
            } else {
                $cart[$productId]['quantity'] = $quantity;
                $message = 'Cart updated successfully';
            }

            Cache::put($cartKey, $cart, now()->addHours(24));

            return $this->successResponse(['quantity' => $quantity], $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update cart', 500);
        }
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request, $productId): JsonResponse
    {
        try {
            $user = $request->user();
            $cartKey = "cart_user_{$user->id}";
            $cart = Cache::get($cartKey, []);

            if (!isset($cart[$productId])) {
                return $this->errorResponse('Item not found in cart', 404);
            }

            unset($cart[$productId]);
            Cache::put($cartKey, $cart, now()->addHours(24));

            return $this->successResponse(null, 'Item removed from cart successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to remove item from cart', 500);
        }
    }

    /**
     * Clear the entire cart.
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $cartKey = "cart_user_{$user->id}";
            
            Cache::forget($cartKey);

            return $this->successResponse(null, 'Cart cleared successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to clear cart', 500);
        }
    }
}