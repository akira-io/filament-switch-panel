# Customization

Learn how to customize the appearance and behavior of the Filament Switch Panel to match your application's design and requirements.

## Publishing Views

To customize the HTML and styling of the switch panel, publish the views to your application:

```bash
php artisan vendor:publish --tag="filament-switch-panel-views"
```

This will create the following files in your application:

```
resources/views/vendor/filament-switch-panel/
├── filament-switch-panel.blade.php      # Main component view
└── components/
    ├── dropdown.blade.php               # Dropdown variant
    ├── icon-button.blade.php            # Icon button variant
    └── modal.blade.php                  # Modal variant
```

## Customizing the Main View

The main view file `filament-switch-panel.blade.php` receives the following variables:

- `$currentPanel`: The currently active panel
- `$canSwitchPanels`: Whether the user can switch panels
- `$heading`: Modal heading text
- `$icons`: Array of custom icons
- `$iconSize`: Size of icons in pixels
- `$isSimple`: Whether to use simple dropdown mode
- `$isSlideOver`: Whether to use slide-over modal
- `$labels`: Array of custom labels
- `$modalWidth`: Width of the modal
- `$panels`: Array of available panels
- `$renderIconAsImage`: Whether icons are image URLs

### Example: Custom Main View

```blade
@if ($canSwitchPanels && count($panels) > 0)
    <div class="px-4 py-2">
        @if ($isSimple)
            {{-- Render simple dropdown --}}
            <x-filament-switch-panel::dropdown :panels="$panels" />
        @else
            {{-- Render modal or slide-over --}}
            <x-filament-switch-panel::modal
                :panels="$panels"
                :heading="$heading"
                :slideOver="$isSlideOver"
                :width="$modalWidth"
            />
        @endif
    </div>
@endif
```

## Customizing Icons

### Using Heroicons

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->icons([
        'admin' => 'heroicon-o-shield-check',
        'user' => 'heroicon-o-user-circle',
        'analytics' => 'heroicon-o-chart-bar',
        'settings' => 'heroicon-o-cog',
    ]);
});
```

### Using Image URLs

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->icons([
        'admin' => 'https://cdn.example.com/admin-icon.png',
        'user' => 'https://cdn.example.com/user-icon.png',
    ], $asImage = true);
});
```

### Using User Avatar Images

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->icons(function (): array {
        $user = auth()->user();

        return [
            'admin' => $user->avatar_url ?? 'heroicon-o-user',
            'user' => $user->profile_image_url ?? 'heroicon-o-user',
        ];
    }, $asImage = true);
});
```

## Customizing Labels

### Static Labels

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->labels([
        'admin' => 'Administration',
        'user' => 'My Dashboard',
        'analytics' => 'Analytics Dashboard',
        'api' => 'API Management',
    ]);
});
```

### Dynamic Labels Based on User

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->labels(function (): array {
        $user = auth()->user();
        $role = $user->role;

        return [
            'admin' => $role === 'super_admin' ? 'Super Admin' : 'Admin Panel',
            'user' => $user->name . "'s Dashboard",
            'team' => 'Team Management',
        ];
    });
});
```

### Localized Labels

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->labels(function (): array {
        return [
            'admin' => __('panels.admin'),
            'user' => __('panels.user'),
            'analytics' => __('panels.analytics'),
        ];
    });
});
```

## Customizing Display Mode

### Simple Dropdown Mode

Display as a simple dropdown select menu:

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->simple(true);
});
```

The simple dropdown is perfect for:
- Space-constrained interfaces
- Quick panel switching
- Minimal visual impact

### Modal Mode (Default)

Display as a full modal dialog:

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->simple(false);
});
```

### Slide-Over Mode

Display as a modal that slides in from the side:

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel
        ->simple(false)
        ->slideOver(true);
});
```

### Responsive Mode Selection

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel
        ->simple(fn (): bool => request()->isMobile())
        ->slideOver(fn (): bool => !request()->isMobile());
});
```

## Customizing Modal Appearance

### Modal Heading

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->modalHeading('Select Your Panel');
});
```

### Modal Width

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->modalWidth('lg');
});
```

Available widths: `xs`, `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `4xl`, `5xl`, `6xl`, `full`, `screen`

### Complete Modal Configuration

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel
        ->simple(false)
        ->slideOver(false)
        ->modalHeading('Admin Panels')
        ->modalWidth('md');
});
```

## Styling with CSS

After publishing the views, you can customize the styling by modifying the Blade templates and adding custom CSS.

### Publishing Package CSS

The package includes Tailwind CSS classes. If you need to override styles, add custom CSS to your application:

```css
/* resources/css/filament-switch-panel.css */

.filament-switch-panel-dropdown {
    /* Custom dropdown styles */
}

.filament-switch-panel-modal {
    /* Custom modal styles */
}

.filament-switch-panel-item {
    /* Custom panel item styles */
}
```

## Complete Customization Example

Here's a comprehensive customization example:

```php
<?php

namespace App\Providers;

use Akira\FilamentSwitchPanel\FilamentSwitchPanel;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
            $user = auth()->user();

            $switchPanel
                // Basic configuration
                ->visible(fn (): bool => auth()->check())
                ->canSwitchPanels(fn (): bool =>
                    $user?->hasAnyRole(['admin', 'manager'])
                )
                ->excludes(fn (): array =>
                    $user?->hasRole('admin') ? [] : ['admin', 'settings']
                )

                // Display mode (responsive)
                ->simple(fn (): bool => request()->isMobile())
                ->slideOver(fn (): bool => !request()->isMobile())

                // Modal customization
                ->modalHeading(fn (): string =>
                    'Panels for ' . ($user?->name ?? 'Guest')
                )
                ->modalWidth(fn (): string =>
                    request()->isMobile() ? 'full' : 'md'
                )

                // Icons (with fallback)
                ->icons(function (): array {
                    return [
                        'admin' => $user?->admin_icon ?? 'heroicon-o-shield-check',
                        'user' => $user?->user_icon ?? 'heroicon-o-user',
                    ];
                })
                ->iconSize(fn (): int => request()->isMobile() ? 24 : 32)

                // Labels (localized)
                ->labels(function (): array {
                    return [
                        'admin' => __('panels.admin'),
                        'user' => __('panels.user'),
                    ];
                })

                // Render location
                ->renderHook('panels::topbar.end');
        });
    }
}
```

## Next Steps

- [Advanced Usage](./advanced-usage.md) - Learn advanced techniques
- [API Reference](./api-reference.md) - View the complete API
- [Configuration](./configuration.md) - Review all configuration options