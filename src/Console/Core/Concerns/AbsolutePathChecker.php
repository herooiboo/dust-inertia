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
}
