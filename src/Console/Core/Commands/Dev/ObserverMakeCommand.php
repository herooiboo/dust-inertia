<?php

namespace Dust\Console\Core\Commands\Dev;

use Dust\Console\Core\Concerns\ModelQualifier;
use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Foundation\Console\ObserverMakeCommand as BaseObserverMakeCommand;

class ObserverMakeCommand extends BaseObserverMakeCommand
{
    use ModelQualifier, OptionsExtender;

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module,
                [
                    'Domain',
                    'Observers',
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }
}
