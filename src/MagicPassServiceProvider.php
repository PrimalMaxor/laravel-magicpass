<?php

namespace Primalmaxor\MagicPass;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Http\Kernel;
use Primalmaxor\MagicPass\Guards\MagicPassGuard;
use Primalmaxor\MagicPass\Http\Middleware\BypassPasswordAuth;

class MagicPassServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'magicpass-migrations');

        $this->publishes([
            __DIR__ . '/../config/magicpass.php' => config_path('magicpass.php'),
        ], 'magicpass-config');

        $this->publishes([
            __DIR__ . '/../resources/views/' => resource_path('views/vendor/magicpass'),
        ], 'magicpass-views');

        $this->publishes([
            __DIR__ . '/../resources/views/emails/' => resource_path('views/vendor/magicpass/emails'),
        ], 'magicpass-email-views');

        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'magicpass');

        $this->overrideAuthConfiguration();
        
        $this->registerMiddleware();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/magicpass.php', 'magicpass'
        );

        Auth::extend('magicpass', function ($app, $name, array $config) {
            return new MagicPassGuard(
                $name,
                Auth::createUserProvider($config['provider']),
                $app['session.store'],
                $app['request']
            );
        });
    }

    protected function overrideAuthConfiguration()
    {
        Config::set('auth.guards.web', [
            'driver' => 'magicpass',
            'provider' => 'users',
        ]);

        Config::set('auth.defaults.guard', 'web');
    }

    protected function registerMiddleware()
    {
        $kernel = $this->app->make(Kernel::class);
        
        $kernel->pushMiddlewareToGroup('web', BypassPasswordAuth::class);
    }
}
