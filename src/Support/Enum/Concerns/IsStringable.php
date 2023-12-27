<?php

namespace Dust\Support\Enum\Concerns;

use UnitEnum;

/**
 * @implements UnitEnum
 */
trait IsStringable
{
    public function toString(): string
    {
        return $this->name;
    }
}
