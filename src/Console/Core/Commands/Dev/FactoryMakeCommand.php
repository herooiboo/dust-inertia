<?php

namespace Dust\Console\Core\Commands\Dev;

use Illuminate\Support\Str;
use Dust\Console\Core\Concerns\ModelQualifier;
use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Database\Console\Factories\FactoryMakeCommand as BaseFactoryMakeCommand;

class FactoryMakeCommand extends BaseFactoryMakeCommand
{
    use OptionsExtender, ModelQualifier;

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($this->laravel->getNamespace(), $module,
                [
                    'Domain',
                    'Database',
                    'Factories',
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function getPath($name): string
    {
        if (! is_null($module = $this->option('module'))) {
            $name = (string) Str::of($name)->replaceFirst(get_module_namespace($this->laravel->getNamespace(), $module, ['Domain', 'Database', 'Factories']), '')->finish('Factory');
            if (str_starts_with($name, '\\')) {
                $name = str_replace('\\', '', $name);
            }

            return get_module_path($module, ['Domain', 'Database', 'Factories', "$name.php"]);
        }

        return parent::getPath($name);
    }

    protected function getNamespace($name): string
    {
        $name = (string) Str::of($name)->replaceFirst('Database\\Factories\\', $this->laravel->getNamespace());

        return parent::getNamespace($name);
    }

    protected function guessModelName($name): array|string
    {
        if (str_ends_with($name, 'Factory')) {
            $name = substr($name, 0, -7);
        }

        $modelName = $this->qualifyModel(Str::after($name, $this->rootNamespace()));

        if (class_exists($modelName)) {
            return $modelName;
        }

        if ($this->hasOption('module') && ($module = $this->option('module'))) {
            $names = explode('\\', $modelName);

            $modelName = array_pop($names);

            return get_module_namespace($this->rootNamespace(), $module, ['Domain', 'Entities', $modelName]);
        }

        if (is_dir(app_path('Models/'))) {
            return $this->rootNamespace().'Models\Model';
        }

        return $this->rootNamespace().'Model';
    }
}
