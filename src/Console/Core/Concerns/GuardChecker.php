<?php

namespace Dust\Console\Core\Concerns;

trait GuardChecker
{
    protected function checkGuard(): string|null
    {
        $guard = $this->option('guard');

        if (! $guard) {
            return null;
        }

        if (! in_array(strtolower($guard), array_keys(config('auth.guards', [])))) {
            return null;
        }

        return ucfirst($guard);
    }
}
