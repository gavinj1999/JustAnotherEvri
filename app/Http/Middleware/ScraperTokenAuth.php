<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ScraperTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.scraper_token');

        if (! $expected) {
            Log::error('SCRAPER_API_TOKEN is not configured.');
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $provided = $request->bearerToken();

        if (! $provided || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
