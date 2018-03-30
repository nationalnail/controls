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
        include __DIR__.'/Routes.php';
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
