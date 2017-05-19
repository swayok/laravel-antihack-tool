<?php

namespace LaravelAntihackTool;

use Illuminate\Support\ServiceProvider;

class AntihackServiceProvider extends ServiceProvider {

    public function boot() {
        // todo: init and run protection and handle saving to db
    }

    public function register() {
        $this->app->singleton(
            'command.antihack.install',
            function ($app) {
                return new AntihackInstallCommand($app['config'], $app['files']);
            }
        );

        $this->publishes([
            __DIR__ . DIRECTORY_SEPARATOR . 'antihack.config.php' => config_path('antihack.php')
        ], 'config');
    }

}