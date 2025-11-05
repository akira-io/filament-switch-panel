# Getting Started

This guide will help you install and set up the Filament Switch Panel package in your Laravel application.

## Installation

### 1. Install via Composer

```bash
composer require akira/filament-switch-panel
```

The package includes a Laravel service provider that will be auto-discovered automatically.

### 2. Publish Assets (Optional)

If you want to customize the views, publish them to your application:

```bash
php artisan vendor:publish --tag="filament-switch-panel-views"
```

This will copy the package views to `resources/views/vendor/filament-switch-panel/`.

### 3. Verify Installation

Once installed, the switch panel will appear in your Filament admin interface by default. You should see a switch panel component in the global search area (before the search bar).

## Basic Setup

By default, the switch panel works without any configuration. However, you can customize it by adding configuration to your service provider.

### Default Behavior

Without any configuration:
- The switch panel appears to all authenticated users
- Only panels the user has access to are shown
- The component displays as a dropdown selector
- Current panel is excluded from the list

### Enabling User Checks

Create or update a service provider to configure the switch panel:

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
                ->visible(fn (): bool => auth()->check())
                ->canSwitchPanels(fn (): bool => auth()->user()?->hasAnyRole(['admin']));
        });
    }
}
```

## User Authorization Methods

The package checks user permissions in two ways:

### 1. Panel Access Check

The switch panel only shows panels that the current user can access. This is determined by:

```php
// If your User model has a canAccessPanel() method
if (method_exists($user, 'canAccessPanel')) {
    return $user->canAccessPanel($panel);
}
```

### 2. Switch Panels Permission

Whether the user can switch panels is determined by:

```php
// If your User model has a canSwitchPanels() method
if (method_exists($user, 'canSwitchPanels')) {
    return $user->canSwitchPanels();
}
```

If these methods don't exist, the permissions fall back to the configured values.

## Example: Setting Up User Model

Here's how to add authorization methods to your User model:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    // ...

    public function canAccessPanel(Panel $panel): bool
    {
        // Only admins and managers can access the admin panel
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole(['admin', 'manager']);
        }

        // Regular users can only access the user panel
        if ($panel->getId() === 'user') {
            return true;
        }

        return false;
    }

    public function canSwitchPanels(): bool
    {
        // Only allow users with admin or manager role to switch panels
        return $this->hasAnyRole(['admin', 'manager']);
    }
}
```

## Filament Compatibility

This package supports both **Filament v3.x** and **Filament v4.x**. The package automatically detects your installed Filament version and adapts accordingly.

### Upgrading to Filament v4

If you upgrade your application to Filament v4:

1. Update your Filament dependency:
```bash
composer require filament/filament:"^4.0" -W
```

2. The switch panel package will work automatically with v4
3. No additional configuration changes are needed

The package automatically handles both versions through version detection in the build configuration.

## Next Steps

Now that you have the package installed, explore these topics:

- [Configuration](./configuration.md) - Learn about configuration options
- [Customization](./customization.md) - Customize the appearance
- [API Reference](./api-reference.md) - View the complete API
- [Advanced Usage](./advanced-usage.md) - Learn advanced techniques

## Troubleshooting

If you encounter any issues during installation, see the [Troubleshooting](./troubleshooting.md) guide.