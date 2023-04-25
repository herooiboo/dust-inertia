<?php

namespace Dust\Console\Core\Commands\Dev;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Dust\Console\Core\Concerns\GuardChecker;
use Dust\Console\Core\Concerns\OptionsExtender;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:service')]
class ServiceMakeCommand extends GeneratorCommand
{
    use OptionsExtender, GuardChecker;

    protected $name = 'make:service';

    protected $description = 'Create a new service class';

    protected $type = 'Service';

    protected function getPath($name): string
    {
        if (! is_null($module = $this->option('module'))) {
            $name = (string) Str::of($name)->replaceFirst(get_module_namespace($this->laravel->getNamespace(), $module, ['Core', 'Services', $this->checkGuard()]), '')->finish('Service');
            if (str_starts_with($name, '\\')) {
                $name = str_replace('\\', '', $name);
            }

            return get_module_path($module, ['Core', 'Services', $this->checkGuard(), "$name.php"]);
        }

        return parent::getPath($name);
    }

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/service.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module,
                [
                    'Core',
                    'Services',
                    $this->checkGuard(),
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function qualifyClass($name): string
    {
        $name = (string) Str::of($name)->ucfirst()->finish('Service');

        return parent::qualifyClass($name);
    }
}
