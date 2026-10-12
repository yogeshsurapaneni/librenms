<?php

namespace NmsApp\Http\Controllers;

use Illuminate\Routing\Controller;
use NmsApp\Support\Branding;

class BrandController extends Controller
{
    public function logo()
    {
        $title = Branding::title() ?: 'LibreNMS';
        $primary = Branding::primary();
        $safe = htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $width = max(170, 12 * mb_strlen($title) + 24);
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$width} 32" width="{$width}" height="32">
  <text x="8" y="22" font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="700" fill="{$primary}">{$safe}</text>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    public function theme()
    {
        $primary = Branding::primary();
        $accent = Branding::accent();
        $css = <<<CSS
/* nms-app brand theme */
.navbar-default { border-bottom: 3px solid {$primary} !important; }
.navbar-default .navbar-nav > .active > a,
.navbar-default .navbar-nav > .open > a,
.navbar-nav > li > a:hover { color: {$primary} !important; }
.btn-primary, .btn-primary:focus { background-color: {$primary}; border-color: {$primary}; }
.btn-primary:hover { background-color: {$accent}; border-color: {$accent}; }
a { color: {$accent}; }
.logon-logo { border-bottom: 3px solid {$primary}; padding-bottom: 8px; margin-bottom: 12px; }
CSS;

        return response($css, 200, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }
}
