<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;
use Barryvdh\DomPDF\ServiceProvider as DomPDFServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

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
        // Helper function to check employee roles
        Blade::directive('employeeHasRole', function ($expression) {
            return "<?php 
                \$employee = null;
                if (Auth::guard('employee')->check()) {
                    \$employee = Auth::guard('employee')->user();
                } elseif (session('logged_in_employee')) {
                    \$employee = session('logged_in_employee');
                }
                
                if (\$employee && (
                    in_array(\$employee->role_id, $expression) || 
                    (isset(\$employee->role) && in_array(\$employee->role->name, $expression))
                )) : 
            ?>";
        });
        
        Blade::directive('endemployeeHasRole', function () {
            return "<?php endif; ?>";
        });
    }
}