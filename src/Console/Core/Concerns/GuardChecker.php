<?php

namespace Dust\Console\Core\Concerns;

use Dust\Exceptions\UnknownGuardException;

trait GuardChecker
{
    /**
     * @throws UnknownGuardException
     */
    protected function checkGuard(): string|null
    {
        $guard = $this->option('guard');

        if (! $guard) {
            return null;
        }

        if (! in_array(strtolower($guard), array_keys(config('auth.guards', [])))) {
            throw new UnknownGuardException($guard);
        }

        return ucfirst($guard);
    }
}
