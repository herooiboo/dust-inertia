<?php

namespace Dust\Console\Core\Concerns;

use Illuminate\Support\Str;

trait ModelQualifier
{
    protected function qualifyModel(string $model): array|string
    {
        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);
        $rootNamespace = $this->rootNamespace();

        if (($startsWithRoot = Str::startsWith($model, $rootNamespace)) || Str::startsWith($model, config('app.modules_path'))) {
            return $startsWithRoot ? $model : $rootNamespace.$model;
        }

        $module = null;
        $path = app_path('Models');

        if ($this->hasOption('module')) {
            $module = $this->option('module');
        } elseif ($this->hasArgument('module')) {
            $module = $this->argument('module');
        }

        if ($module) {
            return get_module_namespace($rootNamespace, $module, ['Domain', 'Entities', $model]);
        }

        return is_dir($path)
            ? $rootNamespace.'Models\\'.$model
            : $rootNamespace.$model;
    }
}
