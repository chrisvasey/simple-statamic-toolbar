<?php

namespace Chrisvasey\SimpleStatamicToolbar;

use Chrisvasey\SimpleStatamicToolbar\Tags\ToolbarTheme;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        ToolbarTheme::class,
    ];

    public function bootAddon(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'simple-statamic-toolbar');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/simple-statamic-toolbar'),
        ], 'simple-statamic-toolbar-views');
    }
}
