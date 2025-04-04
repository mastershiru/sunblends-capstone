<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/auth/google/callback',
        
        'api/refresh-session',
        
        'broadcasting/auth',
        'api/broadcasting/*',  // Add this to exempt all broadcasting routes
        
        'sanctum/csrf-cookie',
        '/api/broadcast/auth',
        'api/*'
    ];
}
