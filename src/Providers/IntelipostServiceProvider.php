<?php

namespace Brew\Intelipost\Providers;

use Brew\Intelipost\Contracts\IntelipostQuoteInterface;
use Brew\Intelipost\Services\IntelipostService;
use Illuminate\Support\ServiceProvider;

class IntelipostServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/intelipost.php', 'intelipost'
        );

        $this->app->bind('intelipost', function ($app) {
            return new IntelipostService;
        });

        $this->app->bind(IntelipostQuoteInterface::class, IntelipostService::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/intelipost.php' => config_path('intelipost.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../../database/migrations/create_intelipost_logs_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_intelipost_logs_table.php'),
        ], 'migrations');
    }
}
