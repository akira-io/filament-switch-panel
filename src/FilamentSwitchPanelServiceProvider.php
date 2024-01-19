<?php

namespace Akira\FilamentSwitchPanel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

use function filament;

/**
 * Class FilamentSwitchPanelServiceProvider
 *
 * This class is a service provider for the Filament Switch Panel package.
 * It extends the PackageServiceProvider class.
 *
 *
 * @see https://github.com/spatie/laravel-package-tools Documentation for laravel-package-tools
 */
class FilamentSwitchPanelServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-switch-panel';

    /**
     * Configure the given package.
     *
     * This class is a Package Service Provider.
     *
     * @param  Package  $package  The package instance.
     *
     * @see https://github.com/spatie/laravel-package-tools Documentation for laravel-package-tools
     */
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */

        $package->name(static::$name)
            ->hasTranslations()
            ->hasViews(static::$name);
    }

    public function packageBooted(): void
    {
        filament()->serving(fn () => FilamentSwitchPanel::boot());
    }
}
