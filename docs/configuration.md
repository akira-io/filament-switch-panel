# Configuration

The Filament Switch Panel provides extensive configuration options through a fluent API. This guide covers all available configuration methods.

## Configuration Methods

Configure the switch panel using the `configureUsing()` static method in your service provider:

```php
use Akira\FilamentSwitchPanel\FilamentSwitchPanel;

FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    // Configuration methods here
});
```

## Available Configuration Options

### Visibility Control

#### `visible(bool|Closure $visible)`

Control whether the switch panel is displayed to users.

```php
// Always visible
$switchPanel->visible(true);

// Always hidden
$switchPanel->visible(false);

// Conditionally visible based on user role
$switchPanel->visible(fn (): bool => auth()->user()?->hasRole('admin'));

// Conditionally visible based on multiple criteria
$switchPanel->visible(fn (): bool =>
    auth()->check() && auth()->user()?->hasAnyRole(['admin', 'manager'])
);
```

### Panel Access Control

#### `canSwitchPanels(bool|Closure $condition)`

Determine whether the user can switch between panels.

```php
// Allow all users to switch panels
$switchPanel->canSwitchPanels(true);

// Only allow users with specific role
$switchPanel->canSwitchPanels(fn (): bool =>
    auth()->user()?->hasRole('admin')
);

// Complex permission check
$switchPanel->canSwitchPanels(fn (): bool =>
    auth()->user()?->can('switch-panels')
);
```

### Excluding Panels

#### `excludes(array|Closure $panelIds)`

Exclude specific panels from the switcher.

```php
// Exclude by panel ID
$switchPanel->excludes(['guest', 'api']);

// Dynamically exclude panels
$switchPanel->excludes(fn (): array =>
    auth()->user()?->hasRole('admin') ? [] : ['admin']
);
```

### Display Modes

#### `simple(bool|Closure $condition)`

Display the switch panel as a simple dropdown selector.

```php
// Use simple dropdown mode
$switchPanel->simple(true);

// Conditionally use simple mode
$switchPanel->simple(fn (): bool => auth()->user()?->is_new);
```

#### `slideOver(bool|Closure $condition)`

Display the switch panel as a slide-over modal instead of a standard modal.

```php
// Use slide-over mode
$switchPanel->slideOver(true);

// Use slide-over on mobile, modal on desktop
$switchPanel->slideOver(fn (): bool => view()->shared('isMobile') ?? false);
```

### Modal Configuration

#### `modalHeading(string|Closure $heading)`

Set the heading displayed in the modal or slide-over.

```php
// Static heading
$switchPanel->modalHeading('Switch Admin Panel');

// Dynamic heading based on user role
$switchPanel->modalHeading(fn (): string =>
    'Select Your Panel - ' . auth()->user()?->role
);
```

#### `modalWidth(string|Closure $width)`

Set the width of the modal. Common values: `xs`, `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `4xl`, `5xl`, `6xl`, `full`, `screen`.

```php
// Fixed width
$switchPanel->modalWidth('md');

// Dynamic width
$switchPanel->modalWidth(fn (): string =>
    view()->shared('isMobile') ? 'full' : 'lg'
);

// Default is 'screen'
```

### Icons

#### `icons(array|Closure $icons, bool $asImage = false)`

Set custom icons for each panel.

```php
// Using Heroicon names
$switchPanel->icons([
    'admin' => 'heroicon-o-shield-check',
    'user' => 'heroicon-o-user',
    'analytics' => 'heroicon-o-chart-bar',
]);

// Using image URLs
$switchPanel->icons([
    'admin' => 'https://example.com/admin.png',
    'user' => 'https://example.com/user.png',
], $asImage = true);

// Dynamic icons based on user
$switchPanel->icons(fn (): array => [
    'admin' => auth()->user()?->avatar_icon ?? 'heroicon-o-user',
]);
```

#### `iconSize(int|Closure|null $size)`

Set the size of the icons in pixels.

```php
// Fixed size
$switchPanel->iconSize(32);

// Dynamic size
$switchPanel->iconSize(fn (): int =>
    view()->shared('isMobile') ? 24 : 32
);

// Default is 32 pixels
```

### Labels

#### `labels(array|Closure $labels)`

Set custom labels for each panel.

```php
// Static labels
$switchPanel->labels([
    'admin' => 'Admin Panel',
    'user' => 'User Dashboard',
    'api' => 'API Management',
]);

// Dynamic labels based on context
$switchPanel->labels(fn (): array => [
    'admin' => auth()->user()?->hasRole('super_admin') ? 'Super Admin' : 'Admin',
    'user' => 'My Dashboard',
]);
```

### Rendering

#### `renderHook(string $hook)`

Customize where the switch panel is rendered in the Filament interface.

Available hooks:
- `panels::global-search.before` (default)
- `panels::global-search.after`
- `panels::topbar.start`
- `panels::topbar.end`
- Custom hooks registered in your application

```php
// Render before global search
$switchPanel->renderHook('panels::global-search.before');

// Render in the topbar
$switchPanel->renderHook('panels::topbar.end');
```

## Complete Configuration Example

Here's a comprehensive example with multiple configuration options:

```php
<?php

namespace App\Providers;

use Akira\FilamentSwitchPanel\FilamentSwitchPanel;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
            $switchPanel
                // Control visibility
                ->visible(fn (): bool => auth()->check())

                // Set permissions
                ->canSwitchPanels(fn (): bool =>
                    auth()->user()?->hasAnyRole(['admin', 'manager'])
                )

                // Exclude certain panels
                ->excludes(['guest'])

                // Use simple dropdown mode
                ->simple(false)

                // Use slide-over modal
                ->slideOver(true)

                // Set modal heading
                ->modalHeading('Select Your Panel')

                // Set modal width
                ->modalWidth('md')

                // Add custom icons
                ->icons([
                    'admin' => 'heroicon-o-shield-check',
                    'user' => 'heroicon-o-user',
                ])

                // Set icon size
                ->iconSize(28)

                // Add custom labels
                ->labels([
                    'admin' => 'Administration',
                    'user' => 'My Account',
                ])

                // Render in specific location
                ->renderHook('panels::topbar.end');
        });
    }
}
```

## Chaining Configuration

All configuration methods return `$this`, allowing you to chain them together:

```php
$switchPanel
    ->visible(true)
    ->simple(true)
    ->modalHeading('Choose Panel')
    ->icons(['admin' => 'heroicon-o-shield'])
    ->labels(['admin' => 'Admin']);
```

## Default Configuration

If no configuration is provided, these are the defaults:

```php
- visible: fn () => canUserAccessPanel() (checks user access)
- canSwitchPanels: true
- excludes: []
- simple: false (uses modal by default)
- slideOver: false
- modalHeading: 'Switch Panels'
- modalWidth: 'screen'
- icons: []
- iconSize: 32
- labels: []
- renderHook: 'panels::global-search.before'
```

## Next Steps

- [Customization](./customization.md) - Learn how to customize the appearance
- [API Reference](./api-reference.md) - View the complete API
- [Advanced Usage](./advanced-usage.md) - Learn advanced techniques