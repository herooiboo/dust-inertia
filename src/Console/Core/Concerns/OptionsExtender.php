<?php

namespace Dust\Console\Core\Concerns;

use Symfony\Component\Console\Input\InputOption;

trait OptionsExtender
{
    use AbsolutePathChecker;

    protected function getOptions(): array
    {
        $options = array_merge(parent::getOptions(), [
            ['module', 'M', InputOption::VALUE_REQUIRED, 'Specify a module.'],
            ['absolute', 'A', InputOption::VALUE_OPTIONAL, 'Specify absolute modules path.'],
        ]);

        if (! $this->hasGuardOption($options)) {
            $options[] = ['guard', 'G', InputOption::VALUE_OPTIONAL, 'Specify guard environment.'];
        }

        return $options;
    }

    public function handle(): void
    {
        $this->checkAbsolutePath();
        parent::handle();
    }

    protected function hasGuardOption(array $options): bool
    {
        foreach ($options as $option) {
            if ($option[0] === 'guard') {
                return true;
            }
        }

        return false;
    }
}
