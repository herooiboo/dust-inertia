<?php

namespace Dust\Console\Core\Commands\Database;

use Illuminate\Support\Composer;
use Dust\Console\Core\Concerns\SignatureExtender;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as BaseMigrateMakeCommand;

class MigrateMakeCommand extends BaseMigrateMakeCommand
{
    use SignatureExtender;

    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        $this->addModuleOption();
        parent::__construct($creator, $composer);
    }

    protected function getMigrationPath(): string
    {
        if (! is_null($targetModule = $this->input->getOption('module'))) {
            $this->input->setOption('realpath', true);

            return get_module_path($targetModule, ['Domain', 'Database', 'Migrations']);
        }

        return parent::getMigrationPath();
    }
}
