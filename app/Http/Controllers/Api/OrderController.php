<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class OrderController extends ApiController
{
    /**
     * Get all orders for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $orders = Order::with(['orderDetails.product'])
                          ->where('user_id', $user->id)
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

            $ordersData = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status ?? 'pending',
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'items' => $order->orderDetails->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'product_id' => $detail->product_id,
                            'product_name' => $detail->product->name ?? 'Unknown Product',
                            'quantity' => $detail->quantity,
                            'price' => $detail->price,
                            'total' => $detail->quantity * $detail->price
                        ];
                    })
                ];
            });

            return $this->successResponse([
                'orders' => $ordersData,
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total()
                ]
            ], 'Orders retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve orders', 500);
        }
    }

    /**
     * Get a specific order by ID.
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $order = Order::with(['orderDetails.product'])
                          ->where('user_id', $user->id)
                          ->findOrFail($id);

            $orderData = [
                'id' => $order->id,
                'total_amount' => $order->total_amount,
                'status' => $order->status ?? 'pending',
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'items' => $order->orderDetails->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'product_id' => $detail->product_id,
                        'product_name' => $detail->product->name ?? 'Unknown Product',
                        'product_image' => $detail->product->image ?? null,
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'total' => $detail->quantity * $detail->price
                    ];
                })
            ];

            return $this->successResponse($orderData, 'Order retrieved successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Order not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve order', 500);
        }
    }

    /**
     * Create a new order from cart items.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1'
            ]);

            $totalAmount = 0;
            $orderItems = [];

            // Validate products and calculate total
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    return $this->errorResponse("Insufficient stock for product: {$product->name}", 400);
                }

                $itemTotal = $product->price * $item['quantity'];
                $totalAmount += $itemTotal;
                
                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $itemTotal
                ];
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]);

            // Create order details and update stock
            foreach ($orderItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Update product stock
                $item['product']->update([
                    'stock' => $item['product']->stock - $item['quantity']
                ]);
            }

            // Clear user's cart
            cache()->forget("cart_{$user->id}");

            // Load order with details for response
            $order->load(['orderDetails.product']);

            $orderData = [
                'id' => $order->id,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'items' => $order->orderDetails->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'product_id' => $detail->product_id,
                        'product_name' => $detail->product->name,
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'total' => $detail->quantity * $detail->price
                    ];
                })
            ];

            return $this->successResponse($orderData, 'Order created successfully', 201);

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Product not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create order', 500);
        }
    }

    /**
     * Update order status (admin functionality).
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:pending,confirmed,shipped,delivered,cancelled'
            ]);

            $order = Order::findOrFail($id);
            
            $order->update([
                'status' => $validated['status']
            ]);

            return $this->successResponse([
                'id' => $order->id,
                'status' => $order->status,
                'updated_at' => $order->updated_at
            ], 'Order status updated successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Order not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update order status', 500);
        }
    }

    /**
     * Cancel an order (only if pending).
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $order = Order::where('user_id', $user->id)
                          ->where('status', 'pending')
                          ->findOrFail($id);

            // Restore product stock
            foreach ($order->orderDetails as $detail) {
                if ($detail->product) {
                    $detail->product->update([
                        'stock' => $detail->product->stock + $detail->quantity
                    ]);
                }
            }

            $order->update(['status' => 'cancelled']);

            return $this->successResponse([
                'id' => $order->id,
                'status' => $order->status,
                'updated_at' => $order->updated_at
            ], 'Order cancelled successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Order not found or cannot be cancelled', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to cancel order', 500);
        }
    }
}