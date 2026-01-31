<?php

namespace Chrisvasey\SimpleStatamicToolbar\Tests;

use Chrisvasey\SimpleStatamicToolbar\ServiceProvider;
use Statamic\Testing\AddonTestCase;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

abstract class TestCase extends AddonTestCase
{
    use PreventsSavingStacheItemsToDisk;

    protected string $addonServiceProvider = ServiceProvider::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addToAssertionCount(3);
    }
}
