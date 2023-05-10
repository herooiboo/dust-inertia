<?php

namespace Dust\Console\Core\Commands\Dev;

use Illuminate\Support\Str;
use Dust\Console\Core\Concerns\GuardChecker;
use Dust\Console\Core\Concerns\OptionsExtender;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Routing\Console\ControllerMakeCommand as BaseControllerMakeCommand;

class ControllerMakeCommand extends BaseControllerMakeCommand
{
    use GuardChecker, OptionsExtender {
        OptionsExtender::getOptions as concernGetOptions;
    }

    public function handle(): void
    {
        $this->checkAbsolutePath();

        if ($this->option('all') && ($module = $this->option('module'))) {
            $name = $this->getNameInput();
            if (str_ends_with($name, 'Controller')) {
                $name = substr($name, 0, -10);
            }

            $this->createRequest($name, $module);
            $this->createResponse($name, $module);
            $this->createService($name, $module);
        }

        parent::handle();
    }

    protected function buildClass($name): string
    {
        $replace = [];
        $baseName = $name;
        if ($this->option('all') && $this->option('module')) {
            if (str_ends_with($name, 'Controller')) {
                $name = substr($name, 0, -10);
            }

            $replace = array_merge($replace, $this->buildRequestReplacements($name));
            $replace = array_merge($replace, $this->buildResponseReplacements($name));
            $replace = array_merge($replace, $this->buildServiceReplacements($name));
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($baseName)
        );
    }

    protected function buildRequestReplacements(string $name): array
    {
        $name = Str::of(class_basename($name))->finish('Request');
        $requestClass = get_module_namespace(trim($this->rootNamespace(), '\\'), $this->option('module'),
            [
                'Http',
                'Requests',
                $this->checkGuard(),
                $name,
            ]
        );

        return [
            '{{ requestNamespace }}' => $requestClass,
            '{{ request }}' => class_basename($requestClass),
        ];
    }

    protected function buildResponseReplacements(string $name): array
    {
        $name = Str::of(class_basename($name))->finish('Response');
        $responseClass = get_module_namespace(trim($this->rootNamespace(), '\\'), $this->option('module'),
            [
                'Http',
                'Responses',
                $this->checkGuard(),
                $name,
            ]
        );

        return [
            '{{ responseNamespace }}' => $responseClass,
            '{{ response }}' => class_basename($responseClass),
        ];
    }

    protected function buildServiceReplacements(string $name): array
    {
        $name = Str::of(class_basename($name))->finish('Service');
        $serviceClass = get_module_namespace(trim($this->rootNamespace(), '\\'), $this->option('module'),
            [
                'Core',
                'Services',
                $this->checkGuard(),
                $name,
            ]
        );

        return [
            '{{ serviceNamespace }}' => $serviceClass,
            '{{ service }}' => class_basename($serviceClass),
        ];
    }

    protected function getOptions(): array
    {
        return array_merge($this->concernGetOptions(), [
            ['all', 'X', InputOption::VALUE_NONE, 'Create controller dependencies.'],
        ]);
    }

    protected function getPath($name): string
    {
        if (! is_null($module = $this->option('module'))) {
            $name = (string) Str::of($name)->replaceFirst(get_module_namespace($this->laravel->getNamespace(), $module, [
                'Http', 'Controllers', $this->checkGuard(),
            ]), '')->finish('Controller');
            if (str_starts_with($name, '\\')) {
                $name = str_replace('\\', '', $name);
            }

            return get_module_path($module, ['Http', 'Controllers', $this->checkGuard(), "$name.php"]);
        }

        return parent::getPath($name);
    }

    protected function getStub(): string
    {
        if (! is_null($this->option('module'))) {
            return $this->resolveStubPath('/stubs/controller.module'.($this->option('all') ? '-all' : '').'.stub');
        }

        return parent::getStub();
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module, ['Http', 'Controllers', $this->checkGuard()]);
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function qualifyClass($name): string
    {
        $name = (string) Str::of($name)->ucfirst()->finish('Controller');

        return parent::qualifyClass($name);
    }

    protected function createRequest(string $name, string $module): void
    {
        $arguments = [
            'name' => $name,
            '--module' => $module,
        ];
        if ($guard = $this->option('guard')) {
            $arguments['--guard'] = $guard;
        }
        $this->call('make:request', $arguments);
    }

    protected function createResponse(string $name, string $module): void
    {
        $arguments = [
            'name' => $name,
            '--module' => $module,
        ];
        if ($guard = $this->option('guard')) {
            $arguments['--guard'] = $guard;
        }
        $this->call('make:response', $arguments);
    }

    protected function createService(string $name, string $module): void
    {
        $arguments = [
            'name' => $name,
            '--module' => $module,
        ];
        if ($guard = $this->option('guard')) {
            $arguments['--guard'] = $guard;
        }
        $this->call('make:service', $arguments);
    }
}
