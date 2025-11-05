# Advanced Usage

Explore advanced techniques and patterns for using the Filament Switch Panel in complex scenarios.

## Role-Based Panel Visibility

### Showing Different Panels to Different Roles

```php
use Akira\FilamentSwitchPanel\FilamentSwitchPanel;

FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $user = auth()->user();

    $switchPanel->excludes(function (): array {
        $user = auth()->user();
        $excludedPanels = [];

        // Super admin can see all panels
        if ($user?->hasRole('super_admin')) {
            return [];
        }

        // Regular admins can't see settings panel
        if ($user?->hasRole('admin')) {
            $excludedPanels[] = 'settings';
        }

        // Managers can only see user and analytics
        if ($user?->hasRole('manager')) {
            $excludedPanels[] = 'admin';
            $excludedPanels[] = 'settings';
        }

        // Regular users can only see their panel
        if ($user?->hasRole('user')) {
            $excludedPanels[] = 'admin';
            $excludedPanels[] = 'analytics';
            $excludedPanels[] = 'settings';
        }

        return $excludedPanels;
    });
});
```

## Permission-Based Panel Access

### Using Laravel Permissions

If you're using a permission library like Spatie's laravel-permission:

```php
use Akira\FilamentSwitchPanel\FilamentSwitchPanel;

FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->excludes(function (): array {
        $user = auth()->user();
        $excludedPanels = [];

        foreach (filament()->getPanels() as $panel) {
            $permissionName = 'access-' . $panel->getId();

            if (!$user?->hasPermissionTo($permissionName)) {
                $excludedPanels[] = $panel->getId();
            }
        }

        return $excludedPanels;
    });
});
```

## Custom User Methods

### Implementing Authorization Methods in Your User Model

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    /**
     * Get panels this user can access
     */
    public function getAccessiblePanels(): array
    {
        return match ($this->role) {
            'super_admin' => ['admin', 'user', 'analytics', 'settings'],
            'admin' => ['admin', 'user', 'analytics'],
            'manager' => ['user', 'analytics'],
            'user' => ['user'],
            default => [],
        };
    }

    /**
     * Check if user can access a specific panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($panel->getId(), $this->getAccessiblePanels());
    }

    /**
     * Check if user can switch between panels
     */
    public function canSwitchPanels(): bool
    {
        return count($this->getAccessiblePanels()) > 1;
    }

    /**
     * Get the icon for this user's primary panel
     */
    public function getPanelIcon(string $panelId): string
    {
        return match ($panelId) {
            'admin' => 'heroicon-o-shield-check',
            'user' => 'heroicon-o-user',
            'analytics' => 'heroicon-o-chart-bar',
            'settings' => 'heroicon-o-cog',
            default => 'heroicon-o-square-2-stack',
        };
    }

    /**
     * Get the label for a panel
     */
    public function getPanelLabel(string $panelId): string
    {
        return match ($panelId) {
            'admin' => 'Administration',
            'user' => 'My Dashboard',
            'analytics' => 'Analytics',
            'settings' => 'Settings',
            default => ucfirst($panelId),
        };
    }
}
```

### Using the User Methods in Configuration

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $user = auth()->user();

    $switchPanel
        ->excludes(function (): array {
            $user = auth()->user();
            $accessiblePanels = $user?->getAccessiblePanels() ?? [];
            $allPanels = filament()->getPanels();

            return collect($allPanels)
                ->map(fn ($panel) => $panel->getId())
                ->reject(fn ($id) => in_array($id, $accessiblePanels))
                ->toArray();
        })
        ->icons(function (): array {
            $user = auth()->user();
            $icons = [];

            foreach (filament()->getPanels() as $panel) {
                $icons[$panel->getId()] = $user?->getPanelIcon($panel->getId())
                    ?? 'heroicon-o-square-2-stack';
            }

            return $icons;
        })
        ->labels(function (): array {
            $user = auth()->user();
            $labels = [];

            foreach (filament()->getPanels() as $panel) {
                $labels[$panel->getId()] = $user?->getPanelLabel($panel->getId())
                    ?? ucfirst($panel->getId());
            }

            return $labels;
        });
});
```

## Organization/Team-Based Panel Access

### Supporting Multi-Tenancy

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        // Only allow access to organization panels
        $organization = $this->currentOrganization();

        if (!$organization) {
            return false;
        }

        // Check if panel is for this organization
        if ($panel->getId() === "org-{$organization->id}") {
            return true;
        }

        // Check if panel is for this user
        if ($panel->getId() === "user") {
            return true;
        }

        // Super admin can access all panels
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return false;
    }

    public function canSwitchPanels(): bool
    {
        return $this->organizations()->count() > 1 || $this->hasRole('super_admin');
    }

    public function currentOrganization()
    {
        return $this->organizations()
            ->wherePivot('is_current', true)
            ->first();
    }
}
```

### Configuration for Multi-Tenancy

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $user = auth()->user();
    $currentOrg = $user->currentOrganization();

    $switchPanel
        ->visible(fn (): bool => $user?->organizations()->count() > 1)
        ->excludes(function (): array {
            $user = auth()->user();
            $excludedPanels = ['api', 'system'];

            // If user doesn't have multiple organizations, exclude organization panels
            if ($user?->organizations()->count() <= 1) {
                $user->organizations()->each(function ($org) use (&$excludedPanels) {
                    $excludedPanels[] = "org-{$org->id}";
                });
            }

            return $excludedPanels;
        })
        ->labels(function (): array {
            $user = auth()->user();
            $labels = ['user' => 'My Dashboard'];

            $user->organizations()->each(function ($org) use (&$labels) {
                $labels["org-{$org->id}"] = $org->name;
            });

            return $labels;
        })
        ->icons(function (): array {
            $user = auth()->user();
            $icons = ['user' => 'heroicon-o-user'];

            $user->organizations()->each(function ($org) use (&$icons) {
                $icons["org-{$org->id}"] = $org->logo_url ?? 'heroicon-o-building-office';
            });

            return $icons;
        });
});
```

## Conditional Rendering Based on Environment

### Development vs Production

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel
        ->visible(fn (): bool => app()->isProduction() || auth()->user()?->isDeveloper())
        ->excludes(fn (): array =>
            app()->isProduction() ? ['debug', 'dev'] : []
        )
        ->simple(fn (): bool => app()->isProduction());
});
```

## Dynamic Panel Icons from Database

### Storing Icons in Database

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPanel extends Model
{
    protected $table = 'admin_panels';

    protected $fillable = [
        'id',
        'name',
        'icon',
        'label',
        'position',
        'is_active',
    ];

    public static function getActiveIcons(): array
    {
        return self::where('is_active', true)
            ->get()
            ->pluck('icon', 'id')
            ->toArray();
    }

    public static function getActiveLabels(): array
    {
        return self::where('is_active', true)
            ->get()
            ->pluck('label', 'id')
            ->toArray();
    }
}
```

### Using Database-Driven Configuration

```php
use App\Models\AdminPanel;
use Akira\FilamentSwitchPanel\FilamentSwitchPanel;

FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel
        ->icons(AdminPanel::getActiveIcons())
        ->labels(AdminPanel::getActiveLabels());
});
```

## Event-Based Panel Switching Tracking

### Tracking User Panel Switches

You might want to track when users switch panels for audit logs:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanelSwitch extends Model
{
    protected $table = 'panel_switches';

    protected $fillable = [
        'user_id',
        'from_panel',
        'to_panel',
        'switched_at',
        'ip_address',
        'user_agent',
    ];

    public static function record(string $toPanel): void
    {
        $user = auth()->user();
        $currentPanel = filament()->getCurrentPanel();

        self::create([
            'user_id' => $user->id,
            'from_panel' => $currentPanel->getId(),
            'to_panel' => $toPanel,
            'switched_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
```

## Caching Configuration

### Improving Performance with Caching

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $user = auth()->user();
    $cacheKey = "panel-config-{$user->id}";

    $excludedPanels = cache()->remember(
        $cacheKey,
        now()->addHours(1),
        function () use ($user) {
            // Calculate excluded panels
            $excluded = [];

            foreach (filament()->getPanels() as $panel) {
                if (!$user->canAccessPanel($panel)) {
                    $excluded[] = $panel->getId();
                }
            }

            return $excluded;
        }
    );

    $switchPanel->excludes($excludedPanels);
});
```

## Advanced Visibility Conditions

### Complex Visibility Logic

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->visible(function (): bool {
        $user = auth()->user();

        // Must be authenticated
        if (!$user) {
            return false;
        }

        // Check if user has access to multiple panels
        $accessibleCount = collect(filament()->getPanels())
            ->filter(fn ($panel) => $user->canAccessPanel($panel))
            ->count();

        if ($accessibleCount <= 1) {
            return false;
        }

        // Check if user has been verified
        if ($user->requires_email_verification && !$user->hasVerifiedEmail()) {
            return false;
        }

        // Check if account is suspended
        if ($user->is_suspended) {
            return false;
        }

        // Check if within business hours (optional)
        if (!$this->isWithinBusinessHours()) {
            return false;
        }

        return true;
    });
});

private function isWithinBusinessHours(): bool
{
    $now = now();
    $hour = $now->hour;

    // Allow access 9 AM to 5 PM
    return $hour >= 9 && $hour < 17;
}
```

## Next Steps

- [API Reference](./api-reference.md) - View the complete API
- [Customization](./customization.md) - Learn customization techniques
- [Troubleshooting](./troubleshooting.md) - Resolve common issues