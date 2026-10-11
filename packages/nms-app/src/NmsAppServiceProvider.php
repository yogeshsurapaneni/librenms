<?php

namespace NmsApp;

use Illuminate\Support\ServiceProvider;

class NmsAppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('nms-auth', \NmsApp\Http\Middleware\EnsureNmsAuth::class);
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
