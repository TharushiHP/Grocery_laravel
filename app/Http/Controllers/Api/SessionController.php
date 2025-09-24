<?php

namespace App\Http\Controllers\Api;

use App\Services\DocumentStore;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SessionController extends ApiController
{
    protected $documentStore;
    
    public function __construct(DocumentStore $documentStore)
    {
        $this->documentStore = $documentStore;
    }
    
    /**
     * Start a new user session (NoSQL storage)
     */
    public function startSession(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'device_type' => 'sometimes|string|in:mobile,tablet,desktop',
                'platform' => 'sometimes|string',
                'app_version' => 'sometimes|string'
            ]);
            
            $sessionData = [
                'user_id' => $request->user()->id ?? null,
                'session_token' => session()->getId(),
                'started_at' => now()->toISOString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $validated['device_type'] ?? 'unknown',
                'platform' => $validated['platform'] ?? 'web',
                'app_version' => $validated['app_version'] ?? '1.0.0',
                'is_active' => true,
                'last_activity' => now()->toISOString(),
                'page_views' => [],
                'actions_count' => 0
            ];
            
            $stored = $this->documentStore->store('user_sessions', $sessionData);
            
            return $this->successResponse([
                'session_id' => $stored['_id'],
                'session_token' => $stored['session_token'],
                'started_at' => $stored['started_at']
            ], 'Session started successfully', 201);
            
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to start session', 500);
        }
    }
    
    /**
     * Update session activity
     */
    public function updateActivity(Request $request, $sessionId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'page_url' => 'sometimes|string',
                'action' => 'sometimes|string',
                'duration_seconds' => 'sometimes|integer|min:0'
            ]);
            
            $session = $this->documentStore->find('user_sessions', $sessionId);
            
            if (!$session) {
                return $this->errorResponse('Session not found', 404);
            }
            
            // Update session data
            $session['last_activity'] = now()->toISOString();
            $session['actions_count'] = ($session['actions_count'] ?? 0) + 1;
            
            if (isset($validated['page_url'])) {
                if (!isset($session['page_views'])) {
                    $session['page_views'] = [];
                }
                $session['page_views'][] = [
                    'url' => $validated['page_url'],
                    'timestamp' => now()->toISOString(),
                    'duration' => $validated['duration_seconds'] ?? null
                ];
            }
            
            if (isset($validated['action'])) {
                if (!isset($session['actions'])) {
                    $session['actions'] = [];
                }
                $session['actions'][] = [
                    'action' => $validated['action'],
                    'timestamp' => now()->toISOString()
                ];
            }
            
            $updated = $this->documentStore->update('user_sessions', $sessionId, $session);
            
            return $this->successResponse([
                'session_id' => $sessionId,
                'last_activity' => $updated['last_activity'],
                'actions_count' => $updated['actions_count']
            ], 'Session activity updated successfully');
            
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->validator);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update session activity', 500);
        }
    }
    
    /**
     * End a session
     */
    public function endSession(Request $request, $sessionId): JsonResponse
    {
        try {
            $session = $this->documentStore->find('user_sessions', $sessionId);
            
            if (!$session) {
                return $this->errorResponse('Session not found', 404);
            }
            
            $session['is_active'] = false;
            $session['ended_at'] = now()->toISOString();
            
            // Calculate session duration
            $startTime = strtotime($session['started_at']);
            $endTime = time();
            $session['duration_seconds'] = $endTime - $startTime;
            
            $updated = $this->documentStore->update('user_sessions', $sessionId, $session);
            
            return $this->successResponse([
                'session_id' => $sessionId,
                'ended_at' => $updated['ended_at'],
                'duration_seconds' => $updated['duration_seconds'],
                'actions_count' => $updated['actions_count'] ?? 0
            ], 'Session ended successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to end session', 500);
        }
    }
    
    /**
     * Get user sessions
     */
    public function getUserSessions(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id ?? null;
            
            if (!$userId) {
                return $this->errorResponse('User not authenticated', 401);
            }
            
            $sessions = $this->documentStore->findWhere('user_sessions', ['user_id' => $userId]);
            
            // Sort by started_at desc
            usort($sessions, function($a, $b) {
                return strtotime($b['started_at']) - strtotime($a['started_at']);
            });
            
            return $this->successResponse([
                'sessions' => $sessions,
                'count' => count($sessions),
                'active_sessions' => count(array_filter($sessions, function($s) { return $s['is_active'] ?? false; }))
            ], 'User sessions retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user sessions', 500);
        }
    }
    
    /**
     * Get session details
     */
    public function getSession($sessionId): JsonResponse
    {
        try {
            $session = $this->documentStore->find('user_sessions', $sessionId);
            
            if (!$session) {
                return $this->errorResponse('Session not found', 404);
            }
            
            return $this->successResponse($session, 'Session details retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve session details', 500);
        }
    }
}