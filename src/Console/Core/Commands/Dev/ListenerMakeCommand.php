<?php

namespace Dust\Console\Core\Commands\Dev;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Dust\Console\Core\Concerns\OptionsExtender;
use Illuminate\Foundation\Console\ListenerMakeCommand as BaseListenerMakeCommand;

class ListenerMakeCommand extends BaseListenerMakeCommand
{
    use OptionsExtender;

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! is_null($module = $this->option('module'))) {
            return get_module_namespace($rootNamespace, $module,
                [
                    'Manager',
                    'Listeners',
                ]
            );
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    protected function buildClass($name): array|string
    {
        $event = $this->option('event');

        if (! Str::startsWith($event, [
            $this->laravel->getNamespace(),
            'Illuminate',
            '\\',
        ])) {
            if ($this->hasOption('module') && ($module = $this->option('module'))) {
                $event = get_module_namespace($this->laravel->getNamespace(), $module, ['Manager', 'Events', $event]);
            } else {
                $event = $this->laravel->getNamespace().'Events\\'.str_replace('/', '\\', $event);
            }
        }

        $stub = str_replace(
            ['DummyEvent', '{{ event }}'], class_basename($event), GeneratorCommand::buildClass($name)
        );

        return str_replace(
            ['DummyFullEvent', '{{ eventNamespace }}'], trim($event, '\\'), $stub
        );
    }
}
