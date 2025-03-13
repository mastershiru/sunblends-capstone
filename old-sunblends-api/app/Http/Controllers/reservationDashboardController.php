<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class reservationDashboardController extends Controller
{
    public function index()
    {
        return view('reservationDashboard');
    }
}
