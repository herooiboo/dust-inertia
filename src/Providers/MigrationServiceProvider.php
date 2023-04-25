<?php

namespace Dust\Providers;

use App\Console\Core\Commands\Database\MigrateMakeCommand;

class MigrationServiceProvider extends \Illuminate\Database\MigrationServiceProvider
{
    public function boot()
    {
        $this->loadModulesMigrations();
    }

    protected function registerMigrateMakeCommand()
    {
        $this->app->singleton(\Illuminate\Database\Console\Migrations\MigrateMakeCommand::class, function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];

            $composer = $app['composer'];

            return new MigrateMakeCommand($creator, $composer);
        });
    }

    protected function loadModulesMigrations(): void
    {
        foreach (app_modules() as $module) {
            $moduleMigrations = get_module_path($module, ['Domain', 'Database', 'Migrations']);
            if (file_exists($moduleMigrations)) {
                $this->loadMigrationsFrom($moduleMigrations);
            }
        }
    }
}
