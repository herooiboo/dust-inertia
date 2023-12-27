<?php

namespace Dust\Console\Core\Commands\Dev;

use Dust\Console\Core\Concerns\GuardChecker;
use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Foundation\Console\ResourceMakeCommand as BaseResourceMakeCommand;

class ResourceMakeCommand extends BaseResourceMakeCommand
{
    use GuardChecker, OptionsExtender;

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module,
                [
                    'Http',
                    'Resources',
                    $this->checkGuard(),
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }
}
