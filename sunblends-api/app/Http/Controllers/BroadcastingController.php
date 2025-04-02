<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class BroadcastingController extends Controller
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        return Broadcast::auth($request);
    }
}