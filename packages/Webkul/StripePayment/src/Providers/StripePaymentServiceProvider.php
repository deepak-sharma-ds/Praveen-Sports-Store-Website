<?php

namespace Webkul\StripePayment\Providers;

use Illuminate\Support\ServiceProvider;

class StripePaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes and views
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'stripepayment');

        // Publish view (optional)
        // $this->publishes([
        //     __DIR__ . '/../../Resources/views' => resource_path('views/vendor/stripepayment'),
        // ], 'views');
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/payment-methods.php',
            'payment_methods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php',
            'core'
        );
    }
}
