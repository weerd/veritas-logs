<?php

namespace Weerd\VeritasLogs;

use Illuminate\Support\ServiceProvider;

class VeritasLogsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'veritas-logs');

        $this->publishes([
            __DIR__.'/../config/veritaslogs.php' => config_path('veritaslogs.php'),
        ], 'veritas-logs-config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
