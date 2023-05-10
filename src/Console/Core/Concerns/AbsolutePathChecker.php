<?php

namespace Dust\Console\Core\Concerns;

trait AbsolutePathChecker
{
    protected function checkAbsolutePath(): void
    {
        if ($absolute = $this->option('absolute', null)) {
            config()->set('app.modules_path', ucfirst($absolute));
        }
    }

    protected function resolveStubPath($stub): string
    {
        if (
            (!$this->hasOption('module') || is_null($this->option('module'))) &&
            (!$this->hasArgument('module') || is_null($this->argument('module')))
        ) {
            return parent::resolveStubPath($stub);
        }

        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.'/../Commands'.$stub;
    }
}
