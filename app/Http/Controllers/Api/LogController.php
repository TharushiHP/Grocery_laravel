<?php

namespace App\Http\Controllers\Api;

use App\Services\DocumentStore;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LogController extends ApiController
{
    protected $documentStore;
    
    public function __construct(DocumentStore $documentStore)
    {
        $this->documentStore = $documentStore;
    }
    
    /**
     * Store API request log
     */
    public function logApiRequest(Request $request): JsonResponse
    {
        try {
            $logData = [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'route' => $request->route()->getName(),
                'user_id' => $request->user()->id ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_headers' => $request->headers->all(),
                'request_body' => $request->all(),
                'timestamp' => now()->toISOString(),
                'response_time_ms' => null, // Will be updated when response is sent
                'status_code' => null,
                'response_size_bytes' => null
            ];
            
            $stored = $this->documentStore->store('api_logs', $logData);
            
            return $this->successResponse([
                'log_id' => $stored['_id'],
                'logged_at' => $stored['timestamp']
            ], 'API request logged successfully', 201);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to log API request', 500);
        }
    }
    
    /**
     * Get API logs with filtering
     */
    public function getLogs(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'method' => 'sometimes|string|in:GET,POST,PUT,DELETE,PATCH',
                'user_id' => 'sometimes|integer',
                'status_code' => 'sometimes|integer',
                'limit' => 'sometimes|integer|min:1|max:1000',
                'date_from' => 'sometimes|date',
                'date_to' => 'sometimes|date'
            ]);
            
            $logs = $this->documentStore->findAll('api_logs');
            
            // Apply filters
            if (isset($validated['method'])) {
                $logs = array_filter($logs, function($log) use ($validated) {
                    return $log['method'] === $validated['method'];
                });
            }
            
            if (isset($validated['user_id'])) {
                $logs = array_filter($logs, function($log) use ($validated) {
                    return $log['user_id'] === $validated['user_id'];
                });
            }
            
            if (isset($validated['status_code'])) {
                $logs = array_filter($logs, function($log) use ($validated) {
                    return $log['status_code'] === $validated['status_code'];
                });
            }
            
            // Date filtering
            if (isset($validated['date_from']) || isset($validated['date_to'])) {
                $logs = array_filter($logs, function($log) use ($validated) {
                    $logDate = date('Y-m-d', strtotime($log['timestamp']));
                    
                    if (isset($validated['date_from']) && $logDate < $validated['date_from']) {
                        return false;
                    }
                    
                    if (isset($validated['date_to']) && $logDate > $validated['date_to']) {
                        return false;
                    }
                    
                    return true;
                });
            }
            
            // Limit results
            $limit = $validated['limit'] ?? 100;
            $logs = array_slice(array_values($logs), 0, $limit);
            
            return $this->successResponse([
                'logs' => $logs,
                'count' => count($logs),
                'filters_applied' => $validated
            ], 'API logs retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve API logs', 500);
        }
    }
    
    /**
     * Get API usage statistics
     */
    public function getUsageStats(): JsonResponse
    {
        try {
            $logs = $this->documentStore->findAll('api_logs');
            
            $stats = [
                'total_requests' => count($logs),
                'requests_by_method' => [],
                'requests_by_status' => [],
                'top_endpoints' => [],
                'top_users' => [],
                'hourly_distribution' => array_fill(0, 24, 0),
                'daily_requests' => [],
                'average_response_time' => 0
            ];
            
            // Group by method
            foreach ($logs as $log) {
                $method = $log['method'];
                $stats['requests_by_method'][$method] = ($stats['requests_by_method'][$method] ?? 0) + 1;
            }
            
            // Group by status code
            foreach ($logs as $log) {
                if (isset($log['status_code'])) {
                    $status = $log['status_code'];
                    $stats['requests_by_status'][$status] = ($stats['requests_by_status'][$status] ?? 0) + 1;
                }
            }
            
            // Top endpoints
            $endpoints = [];
            foreach ($logs as $log) {
                $route = $log['route'] ?? 'unknown';
                $endpoints[$route] = ($endpoints[$route] ?? 0) + 1;
            }
            arsort($endpoints);
            $stats['top_endpoints'] = array_slice($endpoints, 0, 10, true);
            
            // Top users
            $users = [];
            foreach ($logs as $log) {
                if (isset($log['user_id'])) {
                    $userId = $log['user_id'];
                    $users[$userId] = ($users[$userId] ?? 0) + 1;
                }
            }
            arsort($users);
            $stats['top_users'] = array_slice($users, 0, 10, true);
            
            // Hourly distribution
            foreach ($logs as $log) {
                $hour = (int) date('H', strtotime($log['timestamp']));
                $stats['hourly_distribution'][$hour]++;
            }
            
            // Daily requests (last 7 days)
            $dailyRequests = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dailyRequests[$date] = 0;
            }
            
            foreach ($logs as $log) {
                $logDate = date('Y-m-d', strtotime($log['timestamp']));
                if (isset($dailyRequests[$logDate])) {
                    $dailyRequests[$logDate]++;
                }
            }
            $stats['daily_requests'] = $dailyRequests;
            
            // Average response time
            $responseTimes = array_filter(array_column($logs, 'response_time_ms'));
            if (!empty($responseTimes)) {
                $stats['average_response_time'] = round(array_sum($responseTimes) / count($responseTimes), 2);
            }
            
            return $this->successResponse($stats, 'API usage statistics retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve usage statistics', 500);
        }
    }
    
    /**
     * Clear old logs (cleanup functionality)
     */
    public function clearOldLogs(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'days_old' => 'sometimes|integer|min:1|max:365'
            ]);
            
            $daysOld = $validated['days_old'] ?? 30;
            $cutoffDate = now()->subDays($daysOld);
            
            $logs = $this->documentStore->findAll('api_logs');
            $deletedCount = 0;
            
            foreach ($logs as $log) {
                if (strtotime($log['timestamp']) < $cutoffDate->timestamp) {
                    $this->documentStore->delete('api_logs', $log['_id']);
                    $deletedCount++;
                }
            }
            
            return $this->successResponse([
                'deleted_count' => $deletedCount,
                'cutoff_date' => $cutoffDate->toISOString(),
                'remaining_logs' => count($logs) - $deletedCount
            ], 'Old logs cleared successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to clear old logs', 500);
        }
    }
}