<?php

namespace Dust\Console\Core\Commands\Dev;

use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Foundation\Console\ExceptionMakeCommand as BaseExceptionMakeCommand;

class ExceptionMakeCommand extends BaseExceptionMakeCommand
{
    use OptionsExtender;

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module,
                [
                    'Manager',
                    'Exceptions',
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }
}
