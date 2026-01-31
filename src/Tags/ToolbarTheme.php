<?php

namespace Chrisvasey\SimpleStatamicToolbar\Tags;

use Statamic\CP\Color;
use Statamic\Facades\Preference;
use Statamic\Tags\Tags;

class ToolbarTheme extends Tags
{
    protected static $handle = 'toolbar_theme';

    public function index(): array
    {
        $theme = Color::theme();
        $darkTheme = Color::theme(dark: true);

        return [
            'primary' => $theme['primary'] ?? Color::Indigo[700],
            'global_header_bg' => $theme['global-header-bg'] ?? Color::Zinc[800],
            'gray_800' => $theme['gray-800'] ?? Color::Zinc[800],
            'gray_700' => $theme['gray-700'] ?? Color::Zinc[700],
            'dark_primary' => $darkTheme['primary'] ?? Color::Indigo[400],
        ];
    }
}
