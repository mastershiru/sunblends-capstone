<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\HandleCors as Middleware;
use Closure;

class HandleCors extends Middleware
{
    protected $allowedOrigins = ['http://localhost:3000'];
    protected $allowedMethods = ['*'];
    protected $allowedHeaders = ['*'];

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Allow cross-origin access for necessary resources
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        return $response;
    }
}
