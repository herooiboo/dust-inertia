<?php

namespace Dust\Http\Router\Enum;

enum Http
{
    case GET;
    case POST;
    case DELETE;
    case PUT;
    case PATCH;
    case OPTIONS;
    case HEAD;
    case CONNECT;
    case TRACE;
}
