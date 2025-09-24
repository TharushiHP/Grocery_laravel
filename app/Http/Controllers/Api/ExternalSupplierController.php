<?php

namespace App\Http\Controllers\Api;

use App\Services\DocumentStore;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ExternalSupplierController extends ApiController
{
    protected $documentStore;
    
    public function __construct(DocumentStore $documentStore)
    {
        $this->documentStore = $documentStore;
    }
    
    /**
     * External API: Get supplier information and pricing
     */
    public function getSuppliers(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'category' => 'sometimes|string',
                'location' => 'sometimes|string',
                'min_rating' => 'sometimes|numeric|min:1|max:5'
            ]);
            
            // Simulate external supplier API data
            $suppliers = $this->generateSupplierData($validated);
            
            // Store supplier request in NoSQL
            $requestData = [
                'request_type' => 'supplier_lookup',
                'filters' => $validated,
                'suppliers_found' => count($suppliers),
                'request_timestamp' => now()->toISOString(),
                'external_api_simulation' => true,
                'response_time_ms' => rand(100, 500) // Simulate API response time
            ];
            
            $this->documentStore->store('external_api_requests', $requestData);
            
            return $this->successResponse([
                'suppliers' => $suppliers,
                'filters_applied' => $validated,
                'total_found' => count($suppliers),
                'api_source' => 'External Supplier Network',
                'cached_until' => now()->addHours(1)->toISOString()
            ], 'Supplier information retrieved successfully');
            
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve supplier information', 500);
        }
    }
    
    /**
     * External API: Check real-time product availability from suppliers
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_ids' => 'required|array',
                'product_ids.*' => 'integer|exists:products,id',
                'quantity_needed' => 'sometimes|array',
                'urgent' => 'sometimes|boolean'
            ]);
            
            $products = Product::whereIn('id', $validated['product_ids'])->get();
            $availability = [];
            
            foreach ($products as $product) {
                $quantityNeeded = $validated['quantity_needed'][$product->id] ?? 10;
                $urgent = $validated['urgent'] ?? false;
                
                // Simulate external API call to suppliers
                $supplierData = $this->simulateSupplierAvailability($product, $quantityNeeded, $urgent);
                
                $availability[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $product->stock,
                    'quantity_needed' => $quantityNeeded,
                    'suppliers' => $supplierData['suppliers'],
                    'best_price' => $supplierData['best_price'],
                    'fastest_delivery' => $supplierData['fastest_delivery'],
                    'availability_status' => $supplierData['status']
                ];
            }
            
            // Store availability check in NoSQL
            $checkData = [
                'request_type' => 'availability_check',
                'products_checked' => count($products),
                'product_ids' => $validated['product_ids'],
                'urgent_request' => $validated['urgent'] ?? false,
                'suppliers_contacted' => rand(3, 8),
                'check_timestamp' => now()->toISOString(),
                'external_api_calls' => count($products) * 3, // Simulate multiple supplier calls
                'total_response_time_ms' => rand(500, 2000)
            ];
            
            $this->documentStore->store('availability_checks', $checkData);
            
            return $this->successResponse([
                'availability' => $availability,
                'products_checked' => count($products),
                'suppliers_contacted' => $checkData['suppliers_contacted'],
                'check_completed_at' => now()->toISOString()
            ], 'Product availability checked successfully');
            
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to check product availability', 500);
        }
    }
    
    /**
     * External API: Place order with supplier
     */
    public function placeSupplierOrder(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'supplier_id' => 'required|string',
                'products' => 'required|array',
                'products.*.product_id' => 'required|integer|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.agreed_price' => 'required|numeric|min:0',
                'delivery_address' => 'required|string',
                'expected_delivery' => 'required|date|after:today',
                'priority' => 'sometimes|string|in:standard,urgent,express'
            ]);
            
            $totalAmount = 0;
            $orderItems = [];
            
            foreach ($validated['products'] as $item) {
                $product = Product::find($item['product_id']);
                $itemTotal = $item['quantity'] * $item['agreed_price'];
                $totalAmount += $itemTotal;
                
                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['agreed_price'],
                    'total_price' => $itemTotal
                ];
            }
            
            // Simulate external API order placement
            $orderId = 'SUP-' . strtoupper(uniqid());
            $estimatedDelivery = $validated['expected_delivery'];
            
            // Store supplier order in NoSQL
            $orderData = [
                'supplier_order_id' => $orderId,
                'supplier_id' => $validated['supplier_id'],
                'order_items' => $orderItems,
                'total_amount' => $totalAmount,
                'delivery_address' => $validated['delivery_address'],
                'expected_delivery' => $estimatedDelivery,
                'priority' => $validated['priority'] ?? 'standard',
                'order_status' => 'placed',
                'placed_at' => now()->toISOString(),
                'external_api_response' => [
                    'confirmation_number' => $orderId,
                    'estimated_processing_time' => '2-4 hours',
                    'tracking_available' => true
                ]
            ];
            
            $this->documentStore->store('supplier_orders', $orderData);
            
            return $this->successResponse([
                'order_id' => $orderId,
                'supplier_id' => $validated['supplier_id'],
                'total_amount' => $totalAmount,
                'items_count' => count($orderItems),
                'expected_delivery' => $estimatedDelivery,
                'status' => 'order_placed',
                'confirmation' => [
                    'reference_number' => $orderId,
                    'estimated_processing' => '2-4 hours',
                    'tracking_url' => "https://supplier-api.com/track/{$orderId}"
                ]
            ], 'Supplier order placed successfully', 201);
            
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to place supplier order', 500);
        }
    }
    
    /**
     * External API: Get price comparison from multiple suppliers
     */
    public function getPriceComparison(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'sometimes|integer|min:1',
                'include_shipping' => 'sometimes|boolean'
            ]);
            
            $product = Product::find($validated['product_id']);
            $quantity = $validated['quantity'] ?? 1;
            $includeShipping = $validated['include_shipping'] ?? true;
            
            // Simulate multiple supplier price quotes
            $priceComparison = $this->simulatePriceComparison($product, $quantity, $includeShipping);
            
            // Store price comparison in NoSQL
            $comparisonData = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity_requested' => $quantity,
                'suppliers_compared' => count($priceComparison),
                'best_price' => min(array_column($priceComparison, 'total_price')),
                'worst_price' => max(array_column($priceComparison, 'total_price')),
                'average_price' => round(array_sum(array_column($priceComparison, 'total_price')) / count($priceComparison), 2),
                'include_shipping' => $includeShipping,
                'comparison_timestamp' => now()->toISOString(),
                'external_api_calls' => count($priceComparison)
            ];
            
            $this->documentStore->store('price_comparisons', $comparisonData);
            
            return $this->successResponse([
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'current_store_price' => $product->price
                ],
                'quantity' => $quantity,
                'price_comparison' => $priceComparison,
                'summary' => [
                    'suppliers_compared' => count($priceComparison),
                    'best_price' => $comparisonData['best_price'],
                    'average_price' => $comparisonData['average_price'],
                    'potential_savings' => max(0, $product->price - $comparisonData['best_price'])
                ]
            ], 'Price comparison completed successfully');
            
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get price comparison', 500);
        }
    }
    
    /**
     * Get external API usage statistics from NoSQL
     */
    public function getExternalApiStats(): JsonResponse
    {
        try {
            $apiRequests = $this->documentStore->findAll('external_api_requests');
            $availabilityChecks = $this->documentStore->findAll('availability_checks');
            $supplierOrders = $this->documentStore->findAll('supplier_orders');
            $priceComparisons = $this->documentStore->findAll('price_comparisons');
            
            $stats = [
                'total_external_api_calls' => count($apiRequests) + count($availabilityChecks) + count($priceComparisons),
                'supplier_lookups' => count($apiRequests),
                'availability_checks' => count($availabilityChecks),
                'supplier_orders_placed' => count($supplierOrders),
                'price_comparisons' => count($priceComparisons),
                'average_response_time_ms' => 0,
                'most_checked_products' => [],
                'supplier_performance' => [],
                'cost_savings_achieved' => 0
            ];
            
            // Calculate average response time
            $responseTimes = [];
            foreach ([$apiRequests, $availabilityChecks] as $requests) {
                foreach ($requests as $request) {
                    if (isset($request['response_time_ms'])) {
                        $responseTimes[] = $request['response_time_ms'];
                    }
                }
            }
            
            if (!empty($responseTimes)) {
                $stats['average_response_time_ms'] = round(array_sum($responseTimes) / count($responseTimes), 2);
            }
            
            // Calculate potential cost savings
            $totalSavings = 0;
            foreach ($priceComparisons as $comparison) {
                if (isset($comparison['best_price']) && isset($comparison['product_id'])) {
                    $product = Product::find($comparison['product_id']);
                    if ($product && $product->price > $comparison['best_price']) {
                        $totalSavings += ($product->price - $comparison['best_price']) * $comparison['quantity_requested'];
                    }
                }
            }
            $stats['cost_savings_achieved'] = round($totalSavings, 2);
            
            return $this->successResponse($stats, 'External API statistics retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve external API statistics', 500);
        }
    }
    
    /**
     * Simulate supplier data generation
     */
    private function generateSupplierData($filters)
    {
        $suppliers = [
            [
                'id' => 'SUP001',
                'name' => 'Fresh Valley Suppliers',
                'category' => 'Fruits',
                'location' => 'Colombo',
                'rating' => 4.8,
                'delivery_time' => '1-2 days',
                'minimum_order' => 500.00,
                'contact' => 'fresh@valley.lk'
            ],
            [
                'id' => 'SUP002', 
                'name' => 'Green Leaf Distributors',
                'category' => 'Vegetables',
                'location' => 'Kandy',
                'rating' => 4.5,
                'delivery_time' => '2-3 days',
                'minimum_order' => 300.00,
                'contact' => 'orders@greenleaf.lk'
            ],
            [
                'id' => 'SUP003',
                'name' => 'Daily Dairy Co.',
                'category' => 'Dairy',
                'location' => 'Galle',
                'rating' => 4.9,
                'delivery_time' => '1 day',
                'minimum_order' => 200.00,
                'contact' => 'dairy@daily.lk'
            ]
        ];
        
        // Filter suppliers based on criteria
        if (isset($filters['category'])) {
            $suppliers = array_filter($suppliers, function($s) use ($filters) {
                return stripos($s['category'], $filters['category']) !== false;
            });
        }
        
        if (isset($filters['min_rating'])) {
            $suppliers = array_filter($suppliers, function($s) use ($filters) {
                return $s['rating'] >= $filters['min_rating'];
            });
        }
        
        return array_values($suppliers);
    }
    
    /**
     * Simulate supplier availability check
     */
    private function simulateSupplierAvailability($product, $quantity, $urgent)
    {
        $suppliers = [
            [
                'supplier_id' => 'SUP001',
                'supplier_name' => 'Fresh Valley Suppliers',
                'available_quantity' => rand(50, 200),
                'unit_price' => $product->price * (rand(80, 120) / 100),
                'delivery_days' => $urgent ? rand(1, 2) : rand(2, 5)
            ],
            [
                'supplier_id' => 'SUP002',
                'supplier_name' => 'Green Leaf Distributors', 
                'available_quantity' => rand(30, 150),
                'unit_price' => $product->price * (rand(75, 115) / 100),
                'delivery_days' => $urgent ? rand(1, 3) : rand(3, 7)
            ]
        ];
        
        $bestPrice = min(array_column($suppliers, 'unit_price'));
        $fastestDelivery = min(array_column($suppliers, 'delivery_days'));
        $totalAvailable = array_sum(array_column($suppliers, 'available_quantity'));
        
        return [
            'suppliers' => $suppliers,
            'best_price' => $bestPrice,
            'fastest_delivery' => $fastestDelivery . ' days',
            'status' => $totalAvailable >= $quantity ? 'available' : 'limited_availability'
        ];
    }
    
    /**
     * Simulate price comparison from multiple suppliers
     */
    private function simulatePriceComparison($product, $quantity, $includeShipping)
    {
        $suppliers = [
            'Fresh Valley Suppliers' => ['base_price_multiplier' => 0.85, 'shipping_cost' => 25.00],
            'Green Leaf Distributors' => ['base_price_multiplier' => 0.92, 'shipping_cost' => 15.00],
            'Daily Suppliers Ltd' => ['base_price_multiplier' => 0.78, 'shipping_cost' => 35.00],
            'Prime Wholesale' => ['base_price_multiplier' => 0.95, 'shipping_cost' => 10.00]
        ];
        
        $comparison = [];
        foreach ($suppliers as $name => $data) {
            $unitPrice = $product->price * $data['base_price_multiplier'];
            $subtotal = $unitPrice * $quantity;
            $shipping = $includeShipping ? $data['shipping_cost'] : 0;
            $total = $subtotal + $shipping;
            
            $comparison[] = [
                'supplier_name' => $name,
                'unit_price' => round($unitPrice, 2),
                'subtotal' => round($subtotal, 2),
                'shipping_cost' => $shipping,
                'total_price' => round($total, 2),
                'delivery_estimate' => rand(1, 7) . ' days',
                'availability' => rand(50, 500) . ' units'
            ];
        }
        
        // Sort by total price
        usort($comparison, function($a, $b) {
            return $a['total_price'] <=> $b['total_price'];
        });
        
        return $comparison;
    }
}