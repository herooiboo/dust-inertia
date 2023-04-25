<?php

namespace Dust\Console\Core\Commands\Dev;

use LogicException;
use Dust\Console\Core\Concerns\ModelQualifier;
use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Foundation\Console\PolicyMakeCommand as BasePolicyMakeCommand;

class PolicyMakeCommand extends BasePolicyMakeCommand
{
    use OptionsExtender, ModelQualifier;

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module, ['Domain', 'Policies']);
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function userProviderModel()
    {
        $config = $this->laravel['config'];

        $guard = $this->option('guard') ?: $config->get('auth.defaults.guard');

        if (is_null($guardProvider = $config->get('auth.guards.'.$guard.'.provider'))) {
            throw new LogicException('The ['.$guard.'] guard is not defined in your "auth" configuration file.');
        }

        if ($model = $config->get('auth.providers.'.$guardProvider.'.model')) {
            return $model;
        }

        return 'Dust\\Models\\User';
    }
}
