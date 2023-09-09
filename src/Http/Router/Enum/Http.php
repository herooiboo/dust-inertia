<?php

namespace Dust\Http\Router\Enum;

enum Http
{
    case Get;
    case Post;
    case Delete;
    case Put;
    case Patch;
    case Options;
    case Head;
    case Connect;
    case Trace;
}
