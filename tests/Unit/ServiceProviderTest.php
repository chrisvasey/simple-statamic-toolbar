<?php

namespace Chrisvasey\SimpleStatamicToolbar\Tests\Unit;

use Chrisvasey\SimpleStatamicToolbar\Tests\TestCase;

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
}
