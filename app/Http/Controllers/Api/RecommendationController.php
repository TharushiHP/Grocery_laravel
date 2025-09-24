<?php

namespace App\Http\Controllers\Api;

use App\Services\DocumentStore;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class RecommendationController extends ApiController
{
    protected $documentStore;
    
    public function __construct(DocumentStore $documentStore)
    {
        $this->documentStore = $documentStore;
    }
    
    /**
     * Get personalized product recommendations based on cart history
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'sometimes|integer',
                'category' => 'sometimes|string',
                'limit' => 'sometimes|integer|min:1|max:50'
            ]);
            
            $userId = $validated['user_id'] ?? $request->user()->id ?? null;
            $category = $validated['category'] ?? null;
            $limit = $validated['limit'] ?? 10;
            
            // Get user's cart history from NoSQL
            $cartHistory = $this->documentStore->findWhere('cart_analytics', [
                'user_id' => $userId
            ]);
            
            // Get all products from SQL database
            $productsQuery = Product::where('stock', '>', 0);
            if ($category) {
                $productsQuery->where('category', $category);
            }
            $products = $productsQuery->get();
            
            // Simple recommendation algorithm
            $recommendations = $this->generateRecommendations($cartHistory, $products, $limit);
            
            // Store recommendation request in NoSQL
            $requestData = [
                'user_id' => $userId,
                'category_filter' => $category,
                'recommendations_count' => count($recommendations),
                'algorithm_version' => '1.0',
                'request_timestamp' => now()->toISOString(),
                'recommended_products' => array_column($recommendations, 'id')
            ];
            
            $this->documentStore->store('recommendation_requests', $requestData);
            
            return $this->successResponse([
                'recommendations' => $recommendations,
                'algorithm' => 'cart_history_based',
                'user_id' => $userId,
                'category_filter' => $category,
                'total_found' => count($recommendations)
            ], 'Product recommendations generated successfully');
            
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate recommendations', 500);
        }
    }
    
    /**
     * Store cart interaction for recommendation learning
     */
    public function storeCartInteraction(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'action' => 'required|string|in:add,remove,view,purchase',
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'sometimes|integer|min:1',
                'cart_total' => 'sometimes|numeric|min:0',
                'session_id' => 'sometimes|string'
            ]);
            
            $product = Product::find($validated['product_id']);
            $userId = $request->user()->id ?? null;
            
            $interactionData = [
                'user_id' => $userId,
                'action' => $validated['action'],
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category,
                    'price' => $product->price,
                    'stock' => $product->stock
                ],
                'quantity' => $validated['quantity'] ?? 1,
                'cart_total' => $validated['cart_total'] ?? null,
                'session_id' => $validated['session_id'] ?? session()->getId(),
                'timestamp' => now()->toISOString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];
            
            $stored = $this->documentStore->store('cart_analytics', $interactionData);
            
            return $this->successResponse([
                'interaction_id' => $stored['_id'],
                'action' => $validated['action'],
                'product' => $product->name,
                'stored_at' => $stored['created_at']
            ], 'Cart interaction stored successfully', 201);
            
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to store cart interaction', 500);
        }
    }
    
    /**
     * Get frequently bought together products
     */
    public function getFrequentlyBoughtTogether(Request $request, $productId): JsonResponse
    {
        try {
            $product = Product::findOrFail($productId);
            
            // Get all cart interactions for this product
            $interactions = $this->documentStore->findWhere('cart_analytics', [
                'product.id' => (int)$productId,
                'action' => 'purchase'
            ]);
            
            // Find products frequently bought with this one
            $coBoughtProducts = [];
            foreach ($interactions as $interaction) {
                if (isset($interaction['user_id'])) {
                    // Find other products this user bought
                    $userPurchases = $this->documentStore->findWhere('cart_analytics', [
                        'user_id' => $interaction['user_id'],
                        'action' => 'purchase'
                    ]);
                    
                    foreach ($userPurchases as $purchase) {
                        $otherProductId = $purchase['product']['id'];
                        if ($otherProductId !== (int)$productId) {
                            $coBoughtProducts[$otherProductId] = ($coBoughtProducts[$otherProductId] ?? 0) + 1;
                        }
                    }
                }
            }
            
            // Sort by frequency and get top products
            arsort($coBoughtProducts);
            $topProductIds = array_slice(array_keys($coBoughtProducts), 0, 5);
            $topProducts = Product::whereIn('id', $topProductIds)->get();
            
            $recommendations = $topProducts->map(function ($product) use ($coBoughtProducts) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'category' => $product->category,
                    'image' => $product->image,
                    'co_purchase_count' => $coBoughtProducts[$product->id] ?? 0
                ];
            });
            
            // Store this request
            $requestData = [
                'requested_product_id' => (int)$productId,
                'requested_product_name' => $product->name,
                'recommendations_found' => count($recommendations),
                'request_timestamp' => now()->toISOString()
            ];
            
            $this->documentStore->store('frequently_bought_requests', $requestData);
            
            return $this->successResponse([
                'base_product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category
                ],
                'frequently_bought_together' => $recommendations,
                'algorithm' => 'collaborative_filtering',
                'data_points' => count($interactions)
            ], 'Frequently bought together products retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get frequently bought together products', 500);
        }
    }
    
    /**
     * Get cart abandonment analytics
     */
    public function getCartAbandonmentAnalytics(): JsonResponse
    {
        try {
            $cartInteractions = $this->documentStore->findAll('cart_analytics');
            
            $analytics = [
                'total_cart_adds' => 0,
                'total_purchases' => 0,
                'abandonment_rate' => 0,
                'most_abandoned_products' => [],
                'abandonment_by_category' => [],
                'average_time_to_abandon' => 0
            ];
            
            $abandonedProducts = [];
            $categoryStats = [];
            
            foreach ($cartInteractions as $interaction) {
                $category = $interaction['product']['category'] ?? 'unknown';
                
                if ($interaction['action'] === 'add') {
                    $analytics['total_cart_adds']++;
                    
                    if (!isset($categoryStats[$category])) {
                        $categoryStats[$category] = ['adds' => 0, 'purchases' => 0];
                    }
                    $categoryStats[$category]['adds']++;
                    
                } elseif ($interaction['action'] === 'purchase') {
                    $analytics['total_purchases']++;
                    $categoryStats[$category]['purchases']++;
                }
                
                // Track products that were added but not purchased
                if ($interaction['action'] === 'add') {
                    $productId = $interaction['product']['id'];
                    if (!isset($abandonedProducts[$productId])) {
                        $abandonedProducts[$productId] = [
                            'product' => $interaction['product'],
                            'add_count' => 0,
                            'purchase_count' => 0
                        ];
                    }
                    $abandonedProducts[$productId]['add_count']++;
                }
            }
            
            // Calculate abandonment rate
            if ($analytics['total_cart_adds'] > 0) {
                $analytics['abandonment_rate'] = round(
                    (($analytics['total_cart_adds'] - $analytics['total_purchases']) / $analytics['total_cart_adds']) * 100, 
                    2
                );
            }
            
            // Most abandoned products
            foreach ($abandonedProducts as $productId => $data) {
                if ($data['add_count'] > $data['purchase_count']) {
                    $analytics['most_abandoned_products'][] = [
                        'product' => $data['product'],
                        'abandonment_count' => $data['add_count'] - $data['purchase_count'],
                        'abandonment_rate' => round((($data['add_count'] - $data['purchase_count']) / $data['add_count']) * 100, 2)
                    ];
                }
            }
            
            // Sort by abandonment count
            usort($analytics['most_abandoned_products'], function($a, $b) {
                return $b['abandonment_count'] - $a['abandonment_count'];
            });
            $analytics['most_abandoned_products'] = array_slice($analytics['most_abandoned_products'], 0, 10);
            
            // Category abandonment rates
            foreach ($categoryStats as $category => $stats) {
                if ($stats['adds'] > 0) {
                    $analytics['abandonment_by_category'][$category] = round(
                        (($stats['adds'] - $stats['purchases']) / $stats['adds']) * 100,
                        2
                    );
                }
            }
            
            // Store analytics request
            $this->documentStore->store('abandonment_analytics_requests', [
                'total_interactions_analyzed' => count($cartInteractions),
                'generated_at' => now()->toISOString(),
                'abandonment_rate' => $analytics['abandonment_rate']
            ]);
            
            return $this->successResponse($analytics, 'Cart abandonment analytics generated successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate abandonment analytics', 500);
        }
    }
    
    /**
     * Simple recommendation algorithm
     */
    private function generateRecommendations($cartHistory, $products, $limit)
    {
        $recommendations = [];
        $userCategories = [];
        
        // Analyze user's category preferences from cart history
        foreach ($cartHistory as $interaction) {
            if (isset($interaction['product']['category'])) {
                $category = $interaction['product']['category'];
                $userCategories[$category] = ($userCategories[$category] ?? 0) + 1;
            }
        }
        
        // Score products based on user preferences
        foreach ($products as $product) {
            $score = 0;
            
            // Prefer categories user has bought from before
            if (isset($userCategories[$product->category])) {
                $score += $userCategories[$product->category] * 10;
            }
            
            // Prefer products with higher stock (popular items)
            $score += $product->stock;
            
            // Prefer reasonably priced items
            if ($product->price <= 100) {
                $score += 5;
            }
            
            $recommendations[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'category' => $product->category,
                'stock' => $product->stock,
                'image' => $product->image,
                'recommendation_score' => $score,
                'reason' => $this->getRecommendationReason($product, $userCategories)
            ];
        }
        
        // Sort by score and return top results
        usort($recommendations, function($a, $b) {
            return $b['recommendation_score'] - $a['recommendation_score'];
        });
        
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Generate recommendation reason
     */
    private function getRecommendationReason($product, $userCategories)
    {
        if (isset($userCategories[$product->category])) {
            return "You've previously bought {$userCategories[$product->category]} items from {$product->category}";
        }
        
        if ($product->stock > 50) {
            return "Popular item with high availability";
        }
        
        if ($product->price <= 50) {
            return "Great value item";
        }
        
        return "Recommended for you";
    }
}