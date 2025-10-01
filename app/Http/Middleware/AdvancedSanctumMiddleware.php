<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class AdvancedSanctumMiddleware extends EnsureFrontendRequestsAreStateful
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$abilities
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, $next, ...$abilities)
    {
        // Validate token existence and basic authentication
        if (!$request->bearerToken() || !$request->user()) {
            throw new AuthenticationException('Unauthenticated.');
        }

        $token = $request->user()->currentAccessToken();

        // Check token expiration
        if ($token->expires_at && now()->gt($token->expires_at)) {
            $token->delete();
            throw new AuthenticationException('Token has expired.');
        }

        // Validate device ID if present
        $deviceId = $request->header('X-Device-ID');
        if ($deviceId && $token->device_id !== $deviceId) {
            throw new AuthenticationException('Invalid device ID.');
        }

        // Update last used timestamp
        $token->forceFill([
            'last_used_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ])->save();

        // Check token abilities
        if (!empty($abilities)) {
            foreach ($abilities as $ability) {
                if (!$token->can($ability)) {
                    throw new AuthenticationException("Not authorized for {$ability}.");
                }
            }
        }

        return $next($request);
    }
}