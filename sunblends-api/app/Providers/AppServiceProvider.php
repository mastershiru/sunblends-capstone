<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;
use Barryvdh\DomPDF\ServiceProvider as DomPDFServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Excel service
        $this->app->register(ExcelServiceProvider::class);

        // Register DomPDF service
        $this->app->register(DomPDFServiceProvider::class);

        // Add aliases
        $this->app->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);
        $this->app->alias('PDF', \Barryvdh\DomPDF\Facade::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure default options for PDF export if needed
        // You can add any PDF defaults here
    }
}