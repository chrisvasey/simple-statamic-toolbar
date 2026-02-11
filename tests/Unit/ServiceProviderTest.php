<?php

namespace Chrisvasey\SimpleStatamicToolbar\Tests\Unit;

use Chrisvasey\SimpleStatamicToolbar\Http\Middleware\InjectToolbar;
use Chrisvasey\SimpleStatamicToolbar\Tests\TestCase;
use Illuminate\Routing\Router;

class ServiceProviderTest extends TestCase
{
    public function test_views_are_registered()
    {
        $hints = $this->app['view']->getFinder()->getHints();

        $this->assertArrayHasKey('simple-statamic-toolbar', $hints);
    }

    public function test_toolbar_view_exists()
    {
        $this->assertTrue(
            $this->app['view']->exists('simple-statamic-toolbar::components._toolbar')
        );
    }

    public function test_views_are_publishable()
    {
        $publishes = \Illuminate\Support\ServiceProvider::$publishGroups['simple-statamic-toolbar-views'] ?? [];

        $this->assertNotEmpty($publishes);
    }

    public function test_middleware_is_registered_in_web_group()
    {
        $router = $this->app->make(Router::class);
        $middleware = $router->getMiddlewareGroups()['web'] ?? [];

        $this->assertContains(InjectToolbar::class, $middleware);
    }
}
