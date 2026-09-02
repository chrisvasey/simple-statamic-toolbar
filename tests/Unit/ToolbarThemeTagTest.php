<?php

namespace Chrisvasey\SimpleStatamicToolbar\Tests\Unit;

use Chrisvasey\SimpleStatamicToolbar\Tags\ToolbarTheme;
use Chrisvasey\SimpleStatamicToolbar\Tests\TestCase;

class ToolbarThemeTagTest extends TestCase
{
    public function test_tag_is_registered()
    {
        $this->assertTrue(
            app('statamic.tags')->has('toolbar_theme')
        );
    }

    public function test_tag_returns_expected_keys()
    {
        $tag = app(ToolbarTheme::class);
        $tag->setContext(collect());
        $tag->setParameters(collect());

        $result = $tag->index();

        $this->assertArrayHasKey('primary', $result);
        $this->assertArrayHasKey('global_header_bg', $result);
        $this->assertArrayHasKey('gray_800', $result);
        $this->assertArrayHasKey('gray_700', $result);
        $this->assertArrayHasKey('dark_primary', $result);
    }

    public function test_tag_returns_default_colours_when_no_preference_set()
    {
        $tag = app(ToolbarTheme::class);
        $tag->setContext(collect());
        $tag->setParameters(collect());

        $result = $tag->index();

        $this->assertNotEmpty($result['primary']);
        $this->assertNotEmpty($result['global_header_bg']);
    }

    public function test_tag_returns_fallback_colours_on_statamic_five()
    {
        if (class_exists(\Statamic\CP\Color::class)) {
            $this->markTestSkipped('Statamic 5 only.');
        }

        $this->assertSame([
            'primary' => '#4338ca',
            'global_header_bg' => '#27272a',
            'gray_800' => '#27272a',
            'gray_700' => '#3f3f46',
            'dark_primary' => '#818cf8',
        ], ToolbarTheme::index());
    }
}
