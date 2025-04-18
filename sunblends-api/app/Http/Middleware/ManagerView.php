<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ManagerView
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First check if employee is logged in
        if (!Auth::guard('employee')->check()) {
            return redirect('/employee/login');
        }
        
        // Then check if they have either Manager or Super Admin role
        $employee = Auth::guard('employee')->user();
        if (!$employee->hasAnyRole(['Manager', 'Super Admin'])) {
            // They're logged in but don't have the right role
            return redirect('/dashboard')->with('error', 'You do not have permission to access this page.');
        }
        
        return $next($request);
    }
}