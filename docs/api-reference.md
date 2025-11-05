# API Reference

Complete reference for all public methods and properties of the Filament Switch Panel package.

## FilamentSwitchPanel Class

The main component class for the switch panel.

### Static Methods

#### `boot(): void`

Initialize and register the switch panel component. Called automatically by the service provider.

```php
FilamentSwitchPanel::boot();
```

#### `make(): static`

Create a new instance of the switch panel with default configuration.

```php
$switchPanel = FilamentSwitchPanel::make();
```

#### `canUserAccessPanel(): bool`

Static method to check if the currently authenticated user can access panels.

```php
if (FilamentSwitchPanel::canUserAccessPanel()) {
    // User can access panels
}
```

#### `configureUsing(Closure $callback): void`

Configure the switch panel using a callback function.

```php
FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {
    $switchPanel->visible(true)->simple(true);
});
```

### Instance Methods

#### `visible(bool|Closure $visible): static`

Set whether the switch panel is visible.

**Parameters:**
- `$visible` (bool|Closure) - Visibility state or closure

**Returns:** `static` for chaining

```php
$switchPanel->visible(true);
$switchPanel->visible(fn (): bool => auth()->check());
```

#### `canSwitchPanels(bool|Closure $condition): static`

Set whether users can switch panels.

**Parameters:**
- `$condition` (bool|Closure) - Permission condition or closure

**Returns:** `static` for chaining

```php
$switchPanel->canSwitchPanels(true);
$switchPanel->canSwitchPanels(fn (): bool => auth()->user()?->hasRole('admin'));
```

#### `excludes(array|Closure $panelIds): static`

Exclude specific panels from the switcher.

**Parameters:**
- `$panelIds` (array|Closure) - Array of panel IDs or closure

**Returns:** `static` for chaining

```php
$switchPanel->excludes(['guest', 'api']);
$switchPanel->excludes(fn (): array => ['admin']);
```

#### `getExcludes(): array`

Get the list of excluded panel IDs.

**Returns:** array of excluded panel IDs

```php
$excluded = $switchPanel->getExcludes();
```

#### `simple(bool|Closure $condition): static`

Use simple dropdown mode instead of modal.

**Parameters:**
- `$condition` (bool|Closure) - Whether to use simple mode

**Returns:** `static` for chaining

```php
$switchPanel->simple(true);
$switchPanel->simple(fn (): bool => request()->isMobile());
```

#### `isSimple(): bool`

Check if simple mode is enabled.

**Returns:** boolean

```php
if ($switchPanel->isSimple()) {
    // Simple dropdown mode is active
}
```

#### `slideOver(bool|Closure $condition): static`

Use slide-over modal instead of centered modal.

**Parameters:**
- `$condition` (bool|Closure) - Whether to use slide-over

**Returns:** `static` for chaining

```php
$switchPanel->slideOver(true);
$switchPanel->slideOver(fn (): bool => !request()->isMobile());
```

#### `isModalSlideOver(): bool`

Check if slide-over mode is enabled.

**Returns:** boolean

```php
if ($switchPanel->isModalSlideOver()) {
    // Slide-over mode is active
}
```

#### `modalHeading(string|Closure $modalHeading): static`

Set the heading for the modal dialog.

**Parameters:**
- `$modalHeading` (string|Closure) - Modal heading text or closure

**Returns:** `static` for chaining

```php
$switchPanel->modalHeading('Select Your Panel');
$switchPanel->modalHeading(fn (): string => 'Panels for ' . auth()->user()->name);
```

#### `getModalHeading(): string`

Get the modal heading text.

**Returns:** string

```php
$heading = $switchPanel->getModalHeading();
```

#### `modalWidth(string|Closure|null $width): static`

Set the width of the modal dialog.

**Parameters:**
- `$width` (string|Closure|null) - Width class or closure
- Valid values: `xs`, `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `4xl`, `5xl`, `6xl`, `full`, `screen`

**Returns:** `static` for chaining

```php
$switchPanel->modalWidth('md');
$switchPanel->modalWidth(fn (): string => request()->isMobile() ? 'full' : 'lg');
```

#### `getModalWidth(): string`

Get the modal width.

**Returns:** string (default: 'screen')

```php
$width = $switchPanel->getModalWidth();
```

#### `icons(array|Closure $icons, bool $asImage = false): static`

Set custom icons for panels.

**Parameters:**
- `$icons` (array|Closure) - Array of panel icons or closure
- `$asImage` (bool) - Whether icons are image URLs (default: false)

**Returns:** `static` for chaining

**Throws:** Exception if `$asImage` is true and any icon is not a URL

```php
$switchPanel->icons([
    'admin' => 'heroicon-o-shield-check',
    'user' => 'heroicon-o-user',
]);

$switchPanel->icons([
    'admin' => 'https://example.com/admin.png',
], $asImage = true);
```

#### `getIcons(): array`

Get the configured icons.

**Returns:** array of icons

```php
$icons = $switchPanel->getIcons();
```

#### `iconSize(int|Closure|null $size): static`

Set the size of icons in pixels.

**Parameters:**
- `$size` (int|Closure|null) - Icon size in pixels or closure

**Returns:** `static` for chaining

```php
$switchPanel->iconSize(32);
$switchPanel->iconSize(fn (): int => request()->isMobile() ? 24 : 32);
```

#### `getIconSize(): int`

Get the icon size.

**Returns:** integer (default: 32)

```php
$size = $switchPanel->getIconSize();
```

#### `labels(array|Closure $labels): static`

Set custom labels for panels.

**Parameters:**
- `$labels` (array|Closure) - Array of panel labels or closure

**Returns:** `static` for chaining

```php
$switchPanel->labels([
    'admin' => 'Administration',
    'user' => 'My Dashboard',
]);
```

#### `getLabels(): array`

Get the configured labels.

**Returns:** array of labels

```php
$labels = $switchPanel->getLabels();
```

#### `renderHook(string $hook): static`

Set the render hook location for the component.

**Parameters:**
- `$hook` (string) - The render hook name
- Available hooks:
  - `panels::global-search.before` (default)
  - `panels::global-search.after`
  - `panels::topbar.start`
  - `panels::topbar.end`

**Returns:** `static` for chaining

```php
$switchPanel->renderHook('panels::topbar.end');
```

#### `getRenderHookName(): string`

Get the render hook name.

**Returns:** string

```php
$hook = $switchPanel->getRenderHookName();
```

#### `isVisible(): bool`

Check if the switch panel should be visible.

**Returns:** boolean

```php
if ($switchPanel->isVisible()) {
    // Component is visible
}
```

#### `isAbleToSwitchPanels(): bool`

Check if the current user can switch panels.

**Returns:** boolean

```php
if ($switchPanel->isAbleToSwitchPanels()) {
    // User can switch panels
}
```

#### `getCurrentPanel(): Panel`

Get the currently active panel.

**Returns:** Filament\Panel instance

```php
$currentPanel = $switchPanel->getCurrentPanel();
echo $currentPanel->getId();
```

#### `getPanels(): array`

Get the list of available panels for switching.

**Returns:** array of Panel instances (excluding current panel and excluded panels)

```php
$availablePanels = $switchPanel->getPanels();

foreach ($availablePanels as $panel) {
    echo $panel->getId();
}
```

#### `getRenderIconAsImage(): bool`

Check if icons should be rendered as images.

**Returns:** boolean

```php
if ($switchPanel->getRenderIconAsImage()) {
    // Icons are image URLs
}
```

#### `getSwitchPanelView(FilamentSwitchPanel $static): View`

Get the rendered view for the switch panel.

**Parameters:**
- `$static` (FilamentSwitchPanel) - The component instance

**Returns:** Illuminate\Contracts\View\View

```php
$view = $switchPanel->getSwitchPanelView($switchPanel);
echo $view->render();
```

## Filament Helper Functions

### `filament(): FilamentManager`

Get the Filament manager instance.

```php
$currentPanel = filament()->getCurrentPanel();
$defaultPanel = filament()->getDefaultPanel();
$allPanels = filament()->getPanels();
$isServing = filament()->isServing();
```

### `auth(): AuthManager`

Get the authentication manager instance.

```php
$user = auth()->user();
$isAuthenticated = auth()->check();
$isGuest = auth()->guest();
```

## View Variables

When the switch panel is rendered, the following variables are passed to the view:

```php
[
    'currentPanel' => Panel,           // Currently active panel
    'canSwitchPanels' => bool,         // Can user switch panels
    'heading' => string,               // Modal heading
    'icons' => array,                  // Panel icons
    'iconSize' => int,                 // Icon size in pixels
    'isSimple' => bool,                // Simple mode enabled
    'isSlideOver' => bool,             // Slide-over mode enabled
    'labels' => array,                 // Panel labels
    'modalWidth' => string,            // Modal width
    'panels' => array,                 // Available panels
    'renderIconAsImage' => bool,       // Icons are images
]
```

## Configuration Closures

Methods that accept closures are evaluated at render time. This allows dynamic configuration:

```php
$switchPanel->visible(function (): bool {
    // Evaluated on each render
    return auth()->check();
});
```

Closure parameters:
- No parameters are passed to closures
- Access request, auth, and other globals via function scope
- Exceptions are caught and logged by Filament

## Method Chaining

Most configuration methods return `$this`, allowing fluent method chaining:

```php
$switchPanel
    ->visible(true)
    ->simple(true)
    ->modalHeading('Choose Panel')
    ->icons(['admin' => 'heroicon-o-shield'])
    ->labels(['admin' => 'Admin'])
    ->renderHook('panels::topbar.end');
```

## Evaluate Method

The `evaluate()` method is inherited from the Component base class and is used internally to resolve closures:

```php
// Internal usage - you don't need to call this directly
$value = $switchPanel->evaluate($switchPanel->modalHeading);
```

## Events and Hooks

The package uses Filament's render hook system. You can register custom hooks:

```php
use Filament\Support\Facades\FilamentView;

FilamentView::registerRenderHook(
    'panels::global-search.before',
    fn () => view('my-custom-component')
);
```

## Next Steps

- [Configuration](./configuration.md) - Learn about configuration options
- [Advanced Usage](./advanced-usage.md) - Explore advanced techniques
- [Troubleshooting](./troubleshooting.md) - Resolve common issues