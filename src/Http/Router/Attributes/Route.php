<?php

namespace Dust\Http\Router\Attributes;

use Dust\Http\Router\Enum\Http;

#[\Attribute]
class Route
{
    public function __construct(public Http $method, string $uri, string|null $name = null)
    {
    }
}
