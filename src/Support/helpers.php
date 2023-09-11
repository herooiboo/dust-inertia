<?php

if (! function_exists('modules_path')) {
    function modules_path(): string
    {
        return app_path(config('dust.modules.defaults.path'));
    }
}

if (! function_exists('app_modules')) {
    function app_modules(): array
    {
        return array_filter(scandir(modules_path()), fn ($module) => ! in_array($module, ['.', '..']));
    }
}

if (! function_exists('get_module_path')) {
    function get_module_path(string $module, array $subdirectories): string
    {
        $subdirectories = array_filter($subdirectories);

        return implode(DIRECTORY_SEPARATOR, [modules_path(), ucfirst($module), ...$subdirectories]);
    }
}

if (! function_exists('get_module_namespace')) {
    function get_module_namespace(string $rootNamespace, string $module, array $subdirectories, string $modulesRoot = ''): string
    {
        $modulesRoot = $modulesRoot ?: trim(str_replace(app_path(), '', modules_path()), '/');

        $subdirectories = array_filter($subdirectories);

        return implode('\\', [str_replace('\\', '', $rootNamespace), $modulesRoot, ucfirst($module), ...$subdirectories]);
    }
}

if (! function_exists('modules_view_paths')) {
    function modules_view_paths(): array
    {
        return array_filter(array_reduce(app_modules(), function ($paths, $module) {
            $paths[] = get_module_path($module, ['Core', 'View']);

            return $paths;
        }, []), fn ($path) => file_exists($path));
    }
}
