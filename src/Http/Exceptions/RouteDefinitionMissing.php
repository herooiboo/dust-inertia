<?php

namespace Dust\Http\Exceptions;

class RouteDefinitionMissing extends \ErrorException
{
    public function __construct(string $message = 'Route definition is missing!', int $code = 500, int $severity = 1, ?string $filename = __FILE__, ?int $line = __LINE__, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $severity, $filename, $line, $previous);
    }
}
