<?php

namespace Dust\Console\Core\Commands\Dev;

use Illuminate\Support\Str;
use Dust\Console\Core\Concerns\GuardChecker;
use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Foundation\Console\TestMakeCommand as BaseTestMakeCommand;

class TestMakeCommand extends BaseTestMakeCommand
{
    use OptionsExtender, GuardChecker;

    protected function buildClass($name): array|string
    {
        $replace = [];

        if ($module = strtolower($this->option('module'))) {
            $replace = [
                '{{ moduleGroup }}' => "@group $module",
            ];
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    protected function getPath($name): string
    {
        if (! is_null($module = $this->option('module'))) {
            $name = (string) Str::of($name)->replaceFirst(get_module_namespace($this->laravel->getNamespace(), $module, ['Tests', $this->option('unit') ? 'Unit' : 'Feature', $this->checkGuard()]), '')->finish('Test');
            if (str_starts_with($name, '\\')) {
                $name = str_replace('\\', '', $name);
            }

            return get_module_path($module, ['Tests', $this->option('unit') ? 'Unit' : 'Feature', $this->checkGuard(), "$name.php"]);
        }

        return parent::getPath($name);
    }

    protected function rootNamespace(): string
    {
        if (! is_null($this->option('module'))) {
            return 'Dust';
        }

        return parent::rootNamespace();
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module,
                [
                    'Tests',
                    $this->option('unit') ? 'Unit' : 'Feature',
                    $this->checkGuard(),
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function qualifyClass($name): string
    {
        $name = (string) Str::of($name)->ucfirst()->finish('Test');

        return parent::qualifyClass($name);
    }
}
