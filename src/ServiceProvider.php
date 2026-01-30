<?php

namespace Chrisvasey\StatamicToolbar;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'statamic-toolbar');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/statamic-toolbar'),
        ], 'statamic-toolbar-views');
    }
}
