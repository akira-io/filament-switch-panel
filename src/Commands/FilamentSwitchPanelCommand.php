<?php

namespace Akira\FilamentSwitchPanel\Commands;

use Illuminate\Console\Command;

class FilamentSwitchPanelCommand extends Command
{
    public $signature = 'filament-switch-panel';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
