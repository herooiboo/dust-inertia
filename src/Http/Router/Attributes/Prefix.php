<?php

namespace Dust\Http\Router\Attributes;

#[\Attribute]
class Prefix
{
    public function __construct(public string $value)
    {
    }
}
