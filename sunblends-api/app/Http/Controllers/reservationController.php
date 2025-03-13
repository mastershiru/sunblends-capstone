<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Livewire\CustomerReservation;

class reservationController extends Controller
{
    public function index()
    {
        return view('reservation');
    }
}
