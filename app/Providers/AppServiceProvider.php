<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('lang', function ($expression) {
            return "<?php echo __('messages'.$expression); ?>";
        });
    
        Blade::directive('currentLocale', function () {
            return "<?php echo LaravelLocalization::getCurrentLocale(); ?>";
        });
    }
}
