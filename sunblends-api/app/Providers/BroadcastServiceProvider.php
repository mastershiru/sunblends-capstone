<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['web']]);
        
        // Register public channels explicitly 
        Broadcast::channel('orders', function () {
            return true;
        });

        require base_path('routes/channels.php');
    }
}