<?php

namespace App\Http\Middleware;

use App\Models\RegistrationToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistrationToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized: Token is missing'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);

        $registrationToken = RegistrationToken::where('token', $token)->first();

        if (!$registrationToken) {
            return response()->json(['error' => 'Unauthorized: Invalid token'], 401);
        }

        if (now()->greaterThan($registrationToken->expires_at)) {
            return response()->json(['error' => 'Unauthorized: Token expired'], 401);
        }

        return $next($request);
    }
}
