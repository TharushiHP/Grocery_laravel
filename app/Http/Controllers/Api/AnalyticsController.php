<?php

namespace App\Http\Controllers\Api;

use App\Services\DocumentStore;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AnalyticsController extends ApiController
{
    protected $documentStore;
    
    public function __construct(DocumentStore $documentStore)
    {
        $this->documentStore = $documentStore;
    }
    
    /**
     * Store API analytics data (page views, user interactions, etc.)
     */
    public function storeEvent(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'event_type' => 'required|string|in:page_view,product_view,cart_add,cart_remove,search,api_request',
                'user_id' => 'nullable|integer',
                'product_id' => 'nullable|integer',
                'metadata' => 'nullable|array',
                'ip_address' => 'nullable|ip',
                'user_agent' => 'nullable|string'
            ]);
            
            // Add additional data
            $eventData = array_merge($validated, [
                'timestamp' => now()->toISOString(),
                'session_id' => $request->session()->getId(),
                'ip_address' => $validated['ip_address'] ?? $request->ip(),
                'user_agent' => $validated['user_agent'] ?? $request->userAgent()
            ]);
            
            $stored = $this->documentStore->store('analytics_events', $eventData);
            
            return $this->successResponse($stored, 'Analytics event stored successfully', 201);
            
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to store analytics event', 500);
        }
    }
    
    /**
     * Get analytics events with filtering
     */
    public function getEvents(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'event_type' => 'sometimes|string',
                'user_id' => 'sometimes|integer',
                'product_id' => 'sometimes|integer',
                'limit' => 'sometimes|integer|min:1|max:1000'
            ]);
            
            $filters = array_filter([
                'event_type' => $validated['event_type'] ?? null,
                'user_id' => $validated['user_id'] ?? null,
                'product_id' => $validated['product_id'] ?? null,
            ]);
            
            $events = $this->documentStore->findWhere('analytics_events', $filters);
            
            $limit = $validated['limit'] ?? 100;
            $events = array_slice($events, 0, $limit);
            
            return $this->successResponse([
                'events' => $events,
                'count' => count($events),
                'filters_applied' => $filters
            ], 'Analytics events retrieved successfully');
            
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve analytics events', 500);
        }
    }
    
    /**
     * Get analytics dashboard data
     */
    public function getDashboard(): JsonResponse
    {
        try {
            $allEvents = $this->documentStore->findAll('analytics_events');
            
            // Calculate statistics
            $stats = [
                'total_events' => count($allEvents),
                'events_by_type' => [],
                'top_products' => [],
                'recent_activity' => array_slice($allEvents, 0, 10),
                'daily_counts' => []
            ];
            
            // Group by event type
            foreach ($allEvents as $event) {
                $type = $event['event_type'];
                $stats['events_by_type'][$type] = ($stats['events_by_type'][$type] ?? 0) + 1;
            }
            
            // Top products by views
            $productViews = [];
            foreach ($allEvents as $event) {
                if ($event['event_type'] === 'product_view' && isset($event['product_id'])) {
                    $productId = $event['product_id'];
                    $productViews[$productId] = ($productViews[$productId] ?? 0) + 1;
                }
            }
            arsort($productViews);
            $stats['top_products'] = array_slice($productViews, 0, 5, true);
            
            // Daily activity counts (last 7 days)
            $dailyCounts = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dailyCounts[$date] = 0;
            }
            
            foreach ($allEvents as $event) {
                $eventDate = date('Y-m-d', strtotime($event['timestamp']));
                if (isset($dailyCounts[$eventDate])) {
                    $dailyCounts[$eventDate]++;
                }
            }
            $stats['daily_counts'] = $dailyCounts;
            
            return $this->successResponse($stats, 'Analytics dashboard data retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve dashboard data', 500);
        }
    }
    
    /**
     * Get collection statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $collections = ['analytics_events', 'user_sessions', 'api_logs'];
            $stats = [];
            
            foreach ($collections as $collection) {
                $stats[$collection] = $this->documentStore->getStats($collection);
            }
            
            return $this->successResponse([
                'collections' => $stats,
                'total_documents' => array_sum(array_column($stats, 'count')),
                'storage_type' => 'Document Store (NoSQL-like)',
                'generated_at' => now()->toISOString()
            ], 'Document store statistics retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics', 500);
        }
    }
}