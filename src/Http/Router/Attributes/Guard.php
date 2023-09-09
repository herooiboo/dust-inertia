<?php

namespace Dust\Http\Router\Attributes;

#[\Attribute]
class Guard
{
    public function __construct(public string $name)
    {
    }
}
