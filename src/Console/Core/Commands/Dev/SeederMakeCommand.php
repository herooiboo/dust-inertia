<?php

namespace Dust\Console\Core\Commands\Dev;

use Illuminate\Support\Str;
use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Database\Console\Seeds\SeederMakeCommand as BaseSeederMakeCommand;

class SeederMakeCommand extends BaseSeederMakeCommand
{
    use OptionsExtender;

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            if (str_starts_with($rootNamespace, $this->rootNamespace())) {
                return $rootNamespace;
            }

            return get_module_namespace($rootNamespace, $module,
                [
                    'Domain',
                    'Database',
                    'Seeders',
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function getPath($name): string
    {
        if (! is_null($module = $this->option('module'))) {
            $name = (string) Str::of($name)->replaceFirst(get_module_namespace($this->laravel->getNamespace(), $module, ['Domain', 'Database', 'Seeders']), '');
            if (str_starts_with($name, '\\')) {
                $name = str_replace('\\', '', $name);
            }

            return get_module_path($module, ['Domain', 'Database', 'Seeders', "$name.php"]);
        }

        return parent::getPath($name);
    }

    protected function rootNamespace(): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($this->laravel->getNamespace(), $module,
                [
                    'Domain',
                    'Database',
                    'Seeders',
                ]
            );
        }

        return parent::rootNamespace();
    }
}
