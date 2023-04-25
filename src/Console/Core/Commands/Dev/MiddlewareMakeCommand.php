<?php

namespace Dust\Console\Core\Commands\Dev;

use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Routing\Console\MiddlewareMakeCommand as BaseMiddlewareMakeCommand;

class MiddlewareMakeCommand extends BaseMiddlewareMakeCommand
{
    use OptionsExtender;

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module,
                [
                    'Http',
                    'Middleware',
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }
}
