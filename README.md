# Simple Statamic Toolbar

A lightweight Statamic addon that adds a floating toolbar to the frontend for logged-in users. Gives editors quick access to edit the current entry or jump to the Control Panel without leaving the page.

## Features

- **Edit button** — opens the current entry in the CP editor (shown when an `edit_url` is available)
- **Control Panel button** — links to the CP dashboard
- **Collapsible** — toggle open/closed with smooth animation
- **Static cache compatible** — wrapped in `{{ nocache }}` tags
- **Logged-in users only** — hidden from public visitors

## Requirements

- Statamic 6+
- Alpine.js 3.x with the [`@alpinejs/collapse`](https://alpinejs.dev/plugins/collapse) plugin
- Tailwind CSS (the toolbar view uses Tailwind utility classes)

## Installation

```bash
composer require chrisvasey/simple-statamic-toolbar
```

### Alpine.js Collapse Plugin

The toolbar uses Alpine's collapse plugin for its slide animation. Install and register it in your JS entry point:

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

### Tailwind CSS

Add the addon's views to your Tailwind source paths so its utility classes are included in the build:

```css
@source "../../vendor/chrisvasey/simple-statamic-toolbar/resources/views";
```

### Include in Your Layout

Add the toolbar partial before `</body>` in your layout template:

```antlers
{{ partial:simple-statamic-toolbar::components/toolbar }}
```

Then rebuild your frontend assets:

```bash
npm run build
```

## Customisation

Publish the views to override the toolbar template:

```bash
php artisan vendor:publish --tag=simple-statamic-toolbar-views
```

This copies the view to `resources/views/vendor/simple-statamic-toolbar/` where you can modify it freely.

## Testing

```bash
composer test
```

## License

MIT. See [LICENSE](LICENSE) for details.
