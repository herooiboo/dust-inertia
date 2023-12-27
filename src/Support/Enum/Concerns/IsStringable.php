<?php

namespace Dust\Support\Enum\Concerns;

use UnitEnum;

/**
 * @implements UnitEnum
 *
 * @property string $name
 */
trait IsStringable
{
    public function toString(): string
    {
        return $this->name;
    }
}
