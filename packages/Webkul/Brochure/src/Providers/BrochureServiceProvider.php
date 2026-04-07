<?php

namespace Webkul\Brochure\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class BrochureServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin-routes.php');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/shop-routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'brochure');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'brochure');

        // Ensure the public storage symlink works for brochure files
        $this->ensureStorageDirectories();

        // Inject the admin module's style into the admin layout <head>
        Event::listen('bagisto.admin.layout.head', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('brochure::admin.layouts.style');
        });
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/admin-menu.php',
            'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php',
            'acl'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php',
            'core'
        );
    }

    /**
     * Create the brochure storage directories if they don't exist.
     * The actual public symlink must be created via: php artisan storage:link
     */
    protected function ensureStorageDirectories(): void
    {
        $dirs = [
            storage_path('app/public/brochure/pdf'),
            storage_path('app/public/brochure/pages'),
        ];

        foreach ($dirs as $dir) {
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
        }
    }
}
