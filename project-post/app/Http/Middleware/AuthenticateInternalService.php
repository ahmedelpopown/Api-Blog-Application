<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateInternalService
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request,Closure $next): Response {
        $serviceKey = $request->header('X-Service-Key');

        $expectedKey = config('services.comments.secret');

        if ( ! $serviceKey || ! $expectedKey || ! hash_equals($expectedKey, $serviceKey)
        ) {
            return response()->json([
                'message' => 'Unauthorized service.',
            ], 401);
        }

        return $next($request);
    }
}
