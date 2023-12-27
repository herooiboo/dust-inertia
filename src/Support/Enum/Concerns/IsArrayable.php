<?php

namespace Dust\Support\Enum\Concerns;

use UnitEnum;
use Dust\Support\Enum\Contracts\StringableInterface;

/**
 * @implements UnitEnum
 */
trait IsArrayable
{
    public function toArray(): array
    {
        return [
            'text' => $this instanceof StringableInterface ? $this->toString() : $this->name,
            'value' => $this->value,
        ];
    }
}
