<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class dineInController extends Controller
{
    public function index()
    {
        return view('dine_in_order');
    }
}
