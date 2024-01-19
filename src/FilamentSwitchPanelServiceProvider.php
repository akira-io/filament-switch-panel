<?php

namespace Akira\FilamentSwitchPanel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

use function filament;

class FilamentSwitchPanelServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-switch-panel';

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
