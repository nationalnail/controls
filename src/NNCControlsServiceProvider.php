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
        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/nationalnail'),
        ], 'public');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/Routes.php';
        $this->app->bind('nnc', function() {
            return new NNCControlsController;
        });
    }
}
