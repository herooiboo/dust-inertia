<?php

namespace Dust\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Dust\Console\Core\Concerns\AbsolutePathChecker;

#[AsCommand(name: 'make:story')]
class StoryMakeCommand extends Command
{
    use AbsolutePathChecker;

    protected $signature = 'make:story {name : Name of the story} {--M|module= : Name of the module} {--G|guard= : Specify environment guard} {--A|absolute= : Specify modules absolute path}';

    protected $description = 'Create a user story';

    public function handle(): void
    {
        $this->checkAbsolutePath();
        $name = $this->argument('name');
        $module = $this->option('module');
        if (!$module) {
            $this->error('Option module is required | --M|module.');
            return;
        }
        $guard = $this->option('guard');
        $this->createController($name, $module, $guard);
        $this->createTest($name, $module, $guard);
        $this->checkRoutes($module, $guard);
    }

    protected function createController(string $name, string $module, string|null $guard): void
    {
        $arguments = [
            'name'     => $name,
            '--module' => $module,
            '--all'    => true,
        ];

        if ($guard) {
            $arguments['--guard'] = $guard;
        }

        $this->call('make:controller', $arguments);
    }

    protected function createTest(string $name, string $module, string|null $guard): void
    {
        $arguments = [
            'name'     => $name,
            '--module' => $module,
        ];

        if ($guard) {
            $arguments['--guard'] = $guard;
        }
        $this->call('make:test', $arguments);
    }

    protected function checkRoutes($module, string|null $guard): void
    {
        $guard = strtolower($guard ?: 'api');
        $routesFilePath = get_module_path($module, ['Http', 'Routes', "$guard.php"]);

        if (file_exists($routesFilePath)) {
            return;
        }

        $this->initiateRouteFile($routesFilePath);
    }

    protected function initiateRouteFile(string $routesFilePath): void
    {
        $dir = dirname($routesFilePath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($routesFilePath, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n");
    }
}
