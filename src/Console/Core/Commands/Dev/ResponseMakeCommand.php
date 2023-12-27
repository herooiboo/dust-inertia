<?php

namespace Dust\Console\Core\Commands\Dev;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Dust\Console\Core\Concerns\GuardChecker;
use Dust\Console\Core\Concerns\OptionsExtender;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:response')]
class ResponseMakeCommand extends GeneratorCommand
{
    use GuardChecker, OptionsExtender;

    protected $name = 'make:response';

    protected $description = 'Create a new response class';

    protected $type = 'Response';

    protected function getPath($name): string
    {
        if (! is_null($module = $this->option('module'))) {
            $name = (string) Str::of($name)->replaceFirst(get_module_namespace($this->laravel->getNamespace(), $module, ['Http', 'Responses', $this->checkGuard()]), '')->finish('Response');
            if (str_starts_with($name, '\\')) {
                $name = str_replace('\\', '', $name);
            }

            return get_module_path($module, ['Http', 'Responses', $this->checkGuard(), "$name.php"]);
        }

        return parent::getPath($name);
    }

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/response.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module,
                [
                    'Http',
                    'Responses',
                    $this->checkGuard(),
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function qualifyClass($name): string
    {
        $name = (string) Str::of($name)->ucfirst()->finish('Response');

        return parent::qualifyClass($name);
    }
}
