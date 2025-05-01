<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HardenHeaders
{
    protected $headersToRemove = [
        'X-Powered-By',
        'Server',
    ];

    protected $headersToAdd = [
        'X-Content-Type-Options' => 'nosniff',
        'Strict-Transport-Security' => 'max-age:63072000; includeSubDomains; preload',
        'X-Answer' => '42',
        //'X-Powered-By' => 'NCSA HTTPd v1.5',
        //'X-Powered-By' => 'CERN httpd/3.0A',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $response = $next($request);

        $this
            ->removeHeaders($response)
            ->addHeaders($response);

        return $response;
    }

    private function removeHeaders(Response $response): self
    {
        foreach ($this->headersToRemove as $header) {
            header_remove($header); // remove header already stored by php
            $response->headers->remove($header); // remove header managed by laravel
        }
        return $this;
    }

    private function addHeaders(Response $response): self
    {
        foreach ($this->headersToAdd as $header => $value) {
            $response->headers->set($header, $value);
        }
        return $this;
    }

}
