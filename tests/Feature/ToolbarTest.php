<?php

namespace Chrisvasey\SimpleStatamicToolbar\Tests\Feature;

use Chrisvasey\SimpleStatamicToolbar\Tests\TestCase;

class ToolbarTest extends TestCase
{
    private string $templateContent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateContent = file_get_contents(
            __DIR__.'/../../resources/views/components/_toolbar.antlers.html'
        );
    }

    public function test_template_links_to_control_panel()
    {
        $this->assertStringContainsString('{{ cp_url }}', $this->templateContent);
        $this->assertStringContainsString('Control Panel', $this->templateContent);
    }

    public function test_template_links_to_edit_url_when_available()
    {
        $this->assertStringContainsString('{{ if edit_url }}', $this->templateContent);
        $this->assertStringContainsString('{{ edit_url }}', $this->templateContent);
        $this->assertStringContainsString('Edit', $this->templateContent);
    }

    public function test_template_uses_alpine_for_interactivity()
    {
        $this->assertStringContainsString('x-data', $this->templateContent);
        $this->assertStringContainsString('x-show', $this->templateContent);
        $this->assertStringContainsString('x-transition', $this->templateContent);
        $this->assertStringContainsString('x-cloak', $this->templateContent);
    }

    public function test_template_does_not_require_alpine_collapse_plugin()
    {
        $this->assertStringNotContainsString('x-collapse', $this->templateContent);
    }

    public function test_template_has_accessible_toggle_button()
    {
        $this->assertStringContainsString(':aria-label', $this->templateContent);
    }

    public function test_template_opens_links_in_new_tab()
    {
        $this->assertStringContainsString('target="_blank"', $this->templateContent);
        $this->assertStringContainsString('rel="noopener noreferrer"', $this->templateContent);
    }

    public function test_template_uses_theme_css_variables()
    {
        $this->assertStringContainsString('--toolbar-bg:', $this->templateContent);
        $this->assertStringContainsString('--toolbar-bg-hover:', $this->templateContent);
        $this->assertStringContainsString('var(--toolbar-bg)', $this->templateContent);
    }

    public function test_template_uses_scoped_style_block()
    {
        $this->assertStringContainsString('<style>', $this->templateContent);
        $this->assertStringContainsString('.sst-toolbar', $this->templateContent);
        $this->assertStringContainsString('.sst-toggle', $this->templateContent);
        $this->assertStringContainsString('.sst-link', $this->templateContent);
    }

    public function test_template_does_not_use_nocache_wrapper()
    {
        $this->assertStringNotContainsString('{{ nocache }}', $this->templateContent);
    }

    public function test_template_does_not_use_toolbar_theme_tag()
    {
        $this->assertStringNotContainsString('{{ toolbar_theme }}', $this->templateContent);
    }

    public function test_template_defaults_to_closed()
    {
        $this->assertStringNotContainsString("open: true", $this->templateContent);
    }

    public function test_template_persists_state_to_local_storage()
    {
        $this->assertStringContainsString('localStorage.getItem', $this->templateContent);
        $this->assertStringContainsString('localStorage.setItem', $this->templateContent);
    }
}
