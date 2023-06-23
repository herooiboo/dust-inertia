<?php

namespace Dust\Console\Core\Commands\Database;

use Dust\Console\Core\Concerns\OptionsExtender;

class SeedCommand extends \Illuminate\Database\Console\Seeds\SeedCommand
{
    use OptionsExtender;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getSeeder()
    {
        $module = $this->option('module');
        if (! $module) {
            return parent::getSeeder();
        }

        $class = get_module_namespace($this->laravel->getNamespace(), $module,
            [
                'Domain',
                'Database',
                'Seeders',
                $this->option('class'),
            ]);

        return $this->laravel->make($class)
            ->setContainer($this->laravel)
            ->setCommand($this);
    }
}
