<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Removes web links from api paginated response.
 */
class StripPaginationInfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $content = json_decode($response->getContent(), true);

        unset($content['links']);
        unset($content['meta']['links']);
        unset($content['meta']['path']);

        $response->setContent(json_encode($content));

        return $response;
    }
}
