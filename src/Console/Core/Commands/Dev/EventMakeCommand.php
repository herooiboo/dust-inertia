<?php

namespace Dust\Console\Core\Commands\Dev;

use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Foundation\Console\EventMakeCommand as BaseEventMakeCommand;

class EventMakeCommand extends BaseEventMakeCommand
{
    use OptionsExtender;

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module,
                [
                    'Manager',
                    'Events',
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }
}
