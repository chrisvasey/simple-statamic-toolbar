# Statamic Toolbar

A Statamic addon that displays a floating toolbar on the frontend for logged-in users, providing quick access to the CP entry editor and Control Panel dashboard.

## Features

- **Edit button** — opens the current entry in the CP editor (only shown when `edit_url` is available)
- **Control Panel button** — opens the CP dashboard
- **Collapse/expand** — smooth slide animation via Alpine.js Collapse plugin
- **Static cache compatible** — wrapped in `{{ nocache }}` tags
- Only visible to logged-in users

## Requirements

- Statamic 5+
- Alpine.js 3.x
- `@alpinejs/collapse` plugin

## Installation

Add the path repository and require the package in your project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "addons/chrisvasey/statamic-toolbar"
        }
    ],
    "require": {
        "chrisvasey/statamic-toolbar": "*@dev"
    }
}
```

Then run:

```bash
composer update chrisvasey/statamic-toolbar
```

### Alpine.js Collapse Plugin

Install and register the collapse plugin in your JS entry point:

```bash
npm install @alpinejs/collapse
```

```js
import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)

window.Alpine = Alpine
Alpine.start()
```

### Tailwind CSS Source

Add the addon's views to your Tailwind source paths so its classes are included in the build:

```css
@source "../../addons/chrisvasey/statamic-toolbar/resources/views";
```

### Include in Layout

Add the toolbar partial before `</body>` in your layout template:

```antlers
{{ partial:statamic-toolbar::components/toolbar }}
```

Then build your frontend assets:

```bash
npm run build
```

## Customisation

Publish the views to override the toolbar template in your project:

```bash
php artisan vendor:publish --tag=statamic-toolbar-views
```

This copies the view to `resources/views/vendor/statamic-toolbar/` where you can modify it freely.
