<?php

namespace Akira\FilamentSwitchPanel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Akira\FilamentSwitchPanel\FilamentSwitchPanel
 */
class FilamentSwitchPanel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Akira\FilamentSwitchPanel\FilamentSwitchPanel::class;
    }
}
