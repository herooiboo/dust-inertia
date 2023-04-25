<?php

namespace Dust\Support\Enum\Concerns;

use Dust\Support\Enum\Contracts\StringableInterface;

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
