<?php

namespace Tzm\Authorizator;

use Illuminate\Support\ServiceProvider;

class AuthorizatorProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register()
    {
        $this->app->make('Tzm\Authorizator\Controller\AuthorizationController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'authorizator');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/authorizator'),
        ], 'authorizator.views');
        $this->publishes([
            __DIR__ . '/resources' => resource_path('js/vendor/authorizator'),
        ], 'authorizator.vue');
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations')
        ], 'authorizator.migrations');
        $this->publishes([
            __DIR__ . '/routes.php' => base_path('routes/authorizator.php')
        ], 'authorizator.routes');
        $this->publishes([
            __DIR__ . '/Service/AuthorizatorChannels/ExampleChannel.php' => base_path('app/Services/AuthorizatorChannels/ExampleChannel.php')
        ], 'authorizator.example-channel');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Tzm\Authorizator\Console\Commands\DeleteExpiredCodes::class,
            ]);
        }
    }
}
