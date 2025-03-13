<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class LogoutController extends Controller
{
    public function logout()
    {
        Auth::guard('customer')->logout();
        Auth::guard('employee')->logout();
        Session::forget('logged_in_customer');
        Session::forget('logged_in_employee');
        return redirect('/home');
    }
}
