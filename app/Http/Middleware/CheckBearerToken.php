<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class CheckBearerToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');
        if (!$header || !Str::startsWith($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = Str::after($header, 'Bearer ');
        if ($token !== config('services.api.token')) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}
