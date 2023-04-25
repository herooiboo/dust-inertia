<?php

namespace Dust\Console\Core\Concerns;

trait AbsolutePathChecker
{
    protected function checkAbsolutePath(): void
    {
        if ($absolute = $this->option('absolute')) {
            config()->set('app.modules_path', ucfirst($absolute));
        }
    }

    protected function resolveStubPath($stub): string
    {
        if (is_null($this->option('module'))) {
            return parent::resolveStubPath($stub);
        }
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__. '/..' . $stub;
    }
}
