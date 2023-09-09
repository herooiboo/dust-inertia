<?php

namespace Dust\Http\Router\Attributes;

#[\Attribute]
class Middleware
{
    public function __construct(public array $list)
    {
    }
}
