<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderQueueController extends Controller
{
    public function index()
    {
        return view('order-queue-page');
    }
}
