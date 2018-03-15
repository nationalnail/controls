<?php

namespace NNC\Controls;

use Illuminate\Support\ServiceProvider;

class NNCControlsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->app;
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('nnc', function() {
            return new NNCControlsController;
        });
    }
}
