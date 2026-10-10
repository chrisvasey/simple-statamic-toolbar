<?php

namespace Chrisvasey\SimpleStatamicToolbar;

use Chrisvasey\SimpleStatamicToolbar\Http\Middleware\InjectToolbar;
use Chrisvasey\SimpleStatamicToolbar\Tags\ToolbarTheme;
use Illuminate\Routing\Router;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        ToolbarTheme::class,
    ];

    protected $publishables = [
        __DIR__.'/../resources/js/toolbar.js' => 'js/toolbar.js',
    ];

    public function bootAddon(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'simple-statamic-toolbar');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/simple-statamic-toolbar'),
        ], 'simple-statamic-toolbar-views');

        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('web', InjectToolbar::class);
    }
}
