<?php

namespace C4studio\Notification;

use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // load migration --Laravel 5.3
        if (method_exists($this, 'loadMigrationFrom'))
            $this->loadMigrationsFrom(__DIR__ . '/migrations/');
        // provider ability to publish migration via vendor:publish --Laravel 5.2
        else
            $this->publishes([
                __DIR__.'/migrations/' => database_path('migrations')
            ], 'migrations');

        // load package helpers
        require_once(__DIR__ . '/helpers/helpers.php');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // create singleton for use in Facade
        $this->app->singleton('notification', function($app) {
            return new Notification();
        });

        $this->app->alias('notification', 'C4studio\Notification\Notification');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'Notification\Notification',
            'notification'
        );
    }

}