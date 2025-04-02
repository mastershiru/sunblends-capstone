<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Broadcast::routes(['middleware' => ['api', 'auth:sanctum']]);

        // Add a channel authorization rule for private customer channels
        Broadcast::channel('customer.{id}', function ($user, $id) {
            if ($user->tokenCan('customer')) {
                // For customer tokens, check if the customer_id matches
                return (int) $user->customer_id === (int) $id;
            }

            // For admin tokens, allow access to all customer channels
            if ($user->tokenCan('admin') || $user->tokenCan('employee')) {
                return true;
            }

            return false;
        });
    }
}