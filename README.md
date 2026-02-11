# Simple Statamic Toolbar

A lightweight Statamic addon that adds a floating toolbar to the frontend for logged-in users. Gives editors quick access to edit the current entry or jump to the Control Panel without leaving the page.

## Features

- **Zero config** — install and it works immediately
- **Edit button** — opens the current entry in the CP editor (shown when an `edit_url` is available)
- **Control Panel button** — links to the CP dashboard
- **Collapsible** — toggle open/closed with smooth animation
- **Logged-in users only** — hidden from public visitors
- **No frontend dependencies** — uses inline styles (no Tailwind required) and Alpine's built-in transitions (no plugins required)

## Requirements

- Statamic 6+
- Alpine.js 3.x (included with Statamic by default)

## Installation

```bash
composer require chrisvasey/simple-statamic-toolbar
```

That's it. The toolbar will automatically appear for logged-in users on the frontend.

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
