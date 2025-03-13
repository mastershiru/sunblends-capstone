<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Customer;

// Channel for authenticated customers
Broadcast::channel('App.Models.Customer.{id}', function ($user, $id) {
    // Check if user is logged in and authorized to listen to this channel
    if ($user instanceof \App\Models\Customer) {
        return (int) $user->customer_id === (int) $id;
    }
    return false;
});

// Public channel for general order notifications
Broadcast::channel('orders', function () {
    // Public channel, anyone can listen
    return true;
});