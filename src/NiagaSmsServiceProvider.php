<?php

namespace Sontus\LaravelNiagaSms;
use Illuminate\Support\ServiceProvider;
class NiagaSmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge package config with app config
        $this->mergeConfigFrom(__DIR__ . '/../config/niaga-sms.php', 'niaga-sms');

        // Register the service
        $this->app->singleton('niaga-sms', function ($app) {
            return new NiagaSmsService($app['config']['niaga-sms']);
        });
    }

    public function boot()
    {
        // Publish config file
        $this->publishes([
            __DIR__ . '/../config/niaga-sms.php' => config_path('niaga-sms.php'),
        ], 'config');
    }
}
