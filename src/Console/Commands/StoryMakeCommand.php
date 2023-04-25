<?php

namespace Dust\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Dust\Console\Core\Concerns\AbsolutePathChecker;

#[AsCommand(name: 'make:story')]
class StoryMakeCommand extends Command
{
    use AbsolutePathChecker;

    protected $signature = 'make:story {name : name of the story} {module : name of the module} {--guard= : Specify environment guard} {--absolute= : Specify modules absolute path}';

    protected $description = 'Create a user story';

    public function handle()
    {
        $this->checkAbsolutePath();
        $name = $this->argument('name');
        $module = $this->argument('module');
        $guard = $this->option('guard');
        $this->createController($name, $module, $guard);
        $this->createTest($name, $module, $guard);
    }

    protected function createController(string $name, string $module, string|null $guard)
    {
        $arguments = [
            'name' => $name,
            '--module' => $module,
            '--all' => true,
        ];

        if ($guard) {
            $arguments['--guard'] = $guard;
        }

        $this->call('make:controller', $arguments);
    }

    protected function createTest(string $name, string $module, string|null $guard)
    {
        $arguments = [
            'name' => $name,
            '--module' => $module,
        ];

        if ($guard) {
            $arguments['--guard'] = $guard;
        }
        $this->call('make:test', $arguments);
    }
}
