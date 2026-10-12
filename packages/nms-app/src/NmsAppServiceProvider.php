<?php

namespace NmsApp;

use App\Facades\LibrenmsConfig;
use Illuminate\Support\ServiceProvider;
use NmsApp\Support\Branding;

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
        $this->applyBranding();
    }

    private function applyBranding(): void
    {
        $title = Branding::title();
        if ($title === null) {
            return;
        }
        LibrenmsConfig::set('project_name', $title);
        LibrenmsConfig::set('page_title_suffix', $title);
        LibrenmsConfig::set('title_image', 'nms/brand/logo');
        $user = (string) LibrenmsConfig::get('email_user', 'librenms');
        LibrenmsConfig::set('email_from', '"' . $title . '" <' . $user . '@' . php_uname('n') . '>');
        $css = LibrenmsConfig::get('webui.custom_css', []);
        if (! is_array($css)) {
            $css = $css ? [$css] : [];
        }
        if (! in_array('/nms/brand/theme.css', $css, true)) {
            $css[] = '/nms/brand/theme.css';
        }
        LibrenmsConfig::set('webui.custom_css', $css);
    }
}
