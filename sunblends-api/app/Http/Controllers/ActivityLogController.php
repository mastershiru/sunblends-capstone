<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs with role-based access control
     */
    public function index()
    {
        // Make sure the user is logged in
        if (!Auth::guard('employee')->check()) {
            return redirect('/login')->with('error', 'You must be logged in to view activity logs.');
        }
        
        
        
        return view('activity-logs');
    }
}