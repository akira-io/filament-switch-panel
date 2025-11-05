# Troubleshooting

Common issues and their solutions for the Filament Switch Panel package.

## Switch Panel Not Showing

### Issue: The switch panel component doesn't appear in the admin interface

#### Solution 1: Check User Authentication

The switch panel only appears if the user is authenticated. Verify:

```php
// In a controller or middleware
if (auth()->check()) {
    // User is authenticated
}
```

#### Solution 2: Verify Visibility Configuration

Check if the switch panel is set to visible:

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    // Make sure visible is true or returns true from closure
    $switchPanel->visible(true);
});
```

#### Solution 3: Check Available Panels

The switch panel won't show if there's only one accessible panel:

```php
// Debug: Check how many panels are available
$panels = filament()->getPanels();
$accessibleCount = count($panels);

// The component needs at least 2 panels
// excluding the current panel
```

#### Solution 4: Clear Cache

Clear all caches if the panel appeared after installing:

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

#### Solution 5: Check Service Provider

Ensure the service provider is registered. The package auto-discovers it, but verify in `config/app.php`:

```php
'providers' => [
    // ...
    Akira\FilamentSwitchPanel\FilamentSwitchPanelServiceProvider::class,
    // ...
]
```

## Switch Panel Not Functioning Correctly

### Issue: Users can't switch panels or see incorrect panels

#### Solution 1: Verify Panel IDs

Ensure your panel IDs are correct:

```php
// In your Filament config or service provider
foreach (filament()->getPanels() as $panel) {
    echo $panel->getId(); // Print panel IDs
}
```

#### Solution 2: Check User Authorization

Verify your authorization methods are working:

```php
// In your User model
public function canAccessPanel(Panel $panel): bool
{
    dd($panel->getId()); // Debug which panel is being checked
    return true;
}

public function canSwitchPanels(): bool
{
    dd('canSwitchPanels called'); // Debug if method is called
    return true;
}
```

#### Solution 3: Check Excluded Panels

Verify that excluded panels are configured correctly:

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    // Debug: Check excluded panels
    $excluded = $switchPanel->getExcludes();
    dd($excluded); // Should be an array of panel IDs
});
```

#### Solution 4: Clear Filament Cache

Clear Filament's cache:

```bash
php artisan filament:cache-components
```

## Icons Not Displaying

### Issue: Icons are not showing or showing incorrectly

#### Solution 1: Verify Icon Names

Check that Heroicon names are correct. Use the format `heroicon-{style}-{name}`:

```php
// Valid formats
'heroicon-o-user'           // Outline
'heroicon-s-user'           // Solid
'heroicon-m-user'           // Mini
'heroicon-c-user'           // Chevron (if available)
```

#### Solution 2: Check Icon Configuration

Verify icons are configured correctly:

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->icons([
        'admin' => 'heroicon-o-shield-check',
        'user' => 'heroicon-o-user',
        // Make sure all panels have icons defined
    ]);
});
```

#### Solution 3: Using Image Icons

If using image URLs, verify they're accessible:

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    // Validate URLs in development
    $icons = [
        'admin' => 'https://example.com/admin.png',
        'user' => 'https://example.com/user.png',
    ];

    foreach ($icons as $id => $icon) {
        if (!str($icon)->startsWith(['http://', 'https://'])) {
            throw new Exception("Icon for panel '{$id}' must be a valid URL");
        }
    }

    $switchPanel->icons($icons, $asImage = true);
});
```

#### Solution 4: Check Tailwind CSS

Ensure Tailwind CSS is properly configured and compiled:

```bash
npm run build
# or for development
npm run dev
```

## Style Issues

### Issue: Switch panel styling looks broken or incorrect

#### Solution 1: Rebuild CSS and JavaScript

Rebuild the assets:

```bash
npm run build
php artisan view:clear
```

#### Solution 2: Check Tailwind Configuration

Verify the Tailwind config includes Filament paths:

```javascript
// tailwind.config.js
module.exports = {
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
```

#### Solution 3: Publish and Customize Views

If styles are still incorrect, publish and customize the views:

```bash
php artisan vendor:publish --tag="filament-switch-panel-views"
```

Then edit the views in `resources/views/vendor/filament-switch-panel/`

#### Solution 4: Check Dark Mode

If dark mode styling is broken, verify Filament's dark mode is configured:

```php
// In your Filament panel configuration
->darkMode(true)
```

## Modal Not Opening

### Issue: Modal or slide-over doesn't open when clicking the switch panel

#### Solution 1: Check Browser Console

Check the browser's developer console for JavaScript errors:

```javascript
// Open DevTools (F12) and check the Console tab
// for any error messages
```

#### Solution 2: Verify Alpine.js

Ensure Alpine.js is loaded. Filament includes it automatically, but verify in the page source.

#### Solution 3: Check Modal Configuration

Verify modal configuration is correct:

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel
        ->simple(false)  // Make sure not in simple mode
        ->slideOver(false) // Standard modal (try without slideOver)
        ->modalWidth('md')
        ->modalHeading('Select Panel');
});
```

#### Solution 4: Test with Simple Mode

Try using simple mode to see if the basic functionality works:

```php
$switchPanel->simple(true);
```

If simple mode works, the issue is with the modal JavaScript.

## Permissions Not Working

### Issue: Users can see all panels or can't see panels they should access

#### Solution 1: Verify Authorization Methods

Add debugging to your authorization methods:

```php
public function canAccessPanel(Panel $panel): bool
{
    $result = $this->hasRole('admin');

    \Log::info("canAccessPanel check for panel {$panel->getId()}: {$result}");

    return $result;
}
```

Check the logs:

```bash
tail -f storage/logs/laravel.log
```

#### Solution 2: Test Authorization Directly

Test authorization in a controller:

```php
public function test()
{
    $user = auth()->user();
    $panel = filament()->getPanels()[0];

    dd([
        'user' => $user,
        'canAccessPanel' => $user->canAccessPanel($panel),
        'canSwitchPanels' => $user->canSwitchPanels(),
    ]);
}
```

#### Solution 3: Check Role Permissions

Verify roles and permissions are correctly assigned:

```php
// If using spatie/laravel-permission
$user->assignRole('admin');
$user->syncRoles(['admin', 'manager']);

dd($user->getRoleNames()); // Check assigned roles
```

#### Solution 4: Clear Authorization Cache

If using Spatie permissions, clear the permission cache:

```php
\Spatie\Permission\Models\Role::clearCache();
\Spatie\Permission\Models\Permission::clearCache();
```

## Performance Issues

### Issue: Switch panel is slow to load or render

#### Solution 1: Use Closures Instead of Queries

Avoid heavy queries in configuration:

```php
// Bad - executes on every render
$switchPanel->labels(function (): array {
    return User::all()
        ->pluck('name', 'panel')
        ->toArray(); // Slow!
});

// Good - cache or use lightweight data
$switchPanel->labels(function (): array {
    return cache()->remember('panel-labels', now()->addDay(), function () {
        return User::all()
            ->pluck('name', 'panel')
            ->toArray();
    });
});
```

#### Solution 2: Cache Authorization Results

Cache authorization checks:

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $user = auth()->user();
    $cacheKey = "panel-exclude-{$user->id}";

    $excluded = cache()->remember($cacheKey, now()->addHours(1), function () use ($user) {
        $excluded = [];
        foreach (filament()->getPanels() as $panel) {
            if (!$user->canAccessPanel($panel)) {
                $excluded[] = $panel->getId();
            }
        }
        return $excluded;
    });

    $switchPanel->excludes($excluded);
});
```

#### Solution 3: Minimize Evaluations

Avoid running the same evaluation multiple times:

```php
// Bad - calculates multiple times
$user = auth()->user();
$switchPanel->excludes(fn () => $user->getExcludedPanels())
            ->labels(fn () => $user->getExcludedPanels()); // Same method again!

// Good - calculate once
$excluded = $user->getExcludedPanels();
$switchPanel->excludes($excluded);
```

#### Solution 4: Check Network Tab

Check browser DevTools Network tab to see if any requests are slow:

1. Open DevTools (F12)
2. Go to Network tab
3. Reload the page
4. Look for slow requests

## Version Compatibility Issues

### Issue: Package doesn't work with your Filament version

#### Check Supported Versions

Verify your Filament version is supported:

```bash
composer info filament/filament
# Should show v3.x or v4.x
```

#### Filament v3 Compatibility

This package supports Filament v3:

```bash
composer require filament/filament:"^3.0"
```

#### Filament v4 Compatibility

This package supports Filament v4:

```bash
composer require filament/filament:"^4.0" -W
```

#### Update Package

Ensure the switch panel package is up to date:

```bash
composer update akira/filament-switch-panel
```

## Debugging

### Enable Debug Mode

Enable debug logging for the package:

```php
// In a service provider
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    if (app()->isLocal()) {
        // Log all evaluations
        \Log::info('Switch Panel Config', [
            'visible' => $switchPanel->isVisible(),
            'canSwitch' => $switchPanel->isAbleToSwitchPanels(),
            'panels' => count($switchPanel->getPanels()),
            'excluded' => $switchPanel->getExcludes(),
        ]);
    }
});
```

### Use Laravel Debugbar

Install Laravel Debugbar for debugging:

```bash
composer require barryvdh/laravel-debugbar --dev
```

Then monitor the switch panel configuration in the debugbar.

### Check HTML Source

Right-click on the page and select "View Page Source" to check if the switch panel HTML is being generated.

## Getting Help

If you can't resolve the issue:

1. Check the [GitHub Issues](https://github.com/akira/filament-switch-panel/issues)
2. Create a detailed issue with:
   - PHP version
   - Laravel version
   - Filament version
   - Package version
   - Step-by-step reproduction
   - Any error messages
   - Relevant code snippets

3. Check the [Configuration](./configuration.md) guide
4. Review the [API Reference](./api-reference.md)

## Next Steps

- [Configuration](./configuration.md) - Review configuration options
- [Advanced Usage](./advanced-usage.md) - Learn advanced techniques
- [API Reference](./api-reference.md) - View the complete API
- [Getting Started](./getting-started.md) - Review installation and setup