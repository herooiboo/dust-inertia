<?php

namespace Dust\Console\Core\Commands\Dev;

use Illuminate\Support\Str;
use Dust\Console\Core\Concerns\GuardChecker;
use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Foundation\Console\RequestMakeCommand as BaseRequestMakeCommand;

class RequestMakeCommand extends BaseRequestMakeCommand
{
    use OptionsExtender, GuardChecker;

    protected function getPath($name): string
    {
        if (! is_null($module = $this->option('module'))) {
            $name = (string) Str::of($name)->replaceFirst(get_module_namespace($this->laravel->getNamespace(), $module, ['Http', 'Requests', $this->checkGuard()]), '')->finish('Request');
            if (str_starts_with($name, '\\')) {
                $name = str_replace('\\', '', $name);
            }

            return get_module_path($module, ['Http', 'Requests', $this->checkGuard(), "$name.php"]);
        }

        return parent::getPath($name);
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module,
                [
                    'Http',
                    'Requests',
                    $this->checkGuard(),
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function qualifyClass($name): string
    {
        $name = (string) Str::of($name)->ucfirst()->finish('Request');

        return parent::qualifyClass($name);
    }
}
