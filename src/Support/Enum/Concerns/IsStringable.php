<?php

namespace Dust\Support\Enum\Concerns;

trait IsStringable
{
    public function toString(): string
    {
        return $this->name;
    }
}
