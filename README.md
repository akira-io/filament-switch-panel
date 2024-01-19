# Filament Switch Panel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/akira/filament-switch-panel.svg?style=flat-square)](https://packagist.org/packages/akira/filament-switch-panel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/akira/filament-switch-panel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/akira/filament-switch-panel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/akira/filament-switch-panel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/akira/filament-switch-panel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/akira/filament-switch-panel.svg?style=flat-square)](https://packagist.org/packages/akira/filament-switch-panel)

## Installation

You can install the package via composer:

```bash
composer require akira/filament-switch-panel
```

optionally, you can publish the views to customise the switch panel.

```bash
php artisan vendor:publish --tag="filament-switch-panel-views"
```

## Customisation

to start your customisation, use the `configureUsing` method in your service provider's boot method.

```php
use Akira\FilamentSwitchPanel\FilamentSwitchPanel;


FilamentSwitchPanel::configureUsing(function (FilamentSwitchPanel $switchPanel) {

	$switchPanel->modalHeading('Modal Heading')
	->modalWidth('md')
	->slideOver()
	->simple()
	->labels([
		'admin' => 'Admin',
		'user' => 'User',
	])
	->icons([
		'admin' => 'heroicon-o-user',
		'user' => 'heroicon-o-user',
	], $asImage = false)
	->iconSize(32)
	->visible(fn (): bool => auth()->user()?->hasAnyRole(['admin',]))
	->canSwitchPanels(fn (): bool => auth()->user()?->can('switch-panels'))
	->excludes(['user'])
	->renderHook('panels::global-search.before')

});

```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [kidiatoliny](https://github.com/kidiatoliny)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
