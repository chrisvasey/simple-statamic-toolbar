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

    public function test_template_includes_its_own_javascript(): void
    {
        $this->assertStringContainsString('<script>', $this->templateContent);
        $this->assertDoesNotMatchRegularExpression('/\\s(?:x-[\\w:-]+|:aria-label)=/', $this->templateContent);
    }

    public function test_template_has_accessible_toggle_button()
    {
        $this->assertStringContainsString('type="button"', $this->templateContent);
        $this->assertStringContainsString('aria-label="Expand toolbar"', $this->templateContent);
        $this->assertStringContainsString('aria-expanded="false"', $this->templateContent);
        $this->assertStringContainsString('aria-controls="sst-menu"', $this->templateContent);
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
        $this->assertStringContainsString('id="sst-menu" class="sst-menu" hidden', $this->templateContent);
    }

    public function test_template_respects_reduced_motion(): void
    {
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $this->templateContent);
        $this->assertStringContainsString('animation: none;', $this->templateContent);
        $this->assertStringContainsString('transition: none;', $this->templateContent);
    }

    public function test_template_persists_state_to_local_storage()
    {
        $this->assertStringContainsString('localStorage.getItem', $this->templateContent);
        $this->assertStringContainsString('localStorage.setItem', $this->templateContent);
    }
}
