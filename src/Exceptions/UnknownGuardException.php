<?php

namespace Dust\Exceptions;

use Exception;
use Throwable;

class UnknownGuardException extends Exception
{
    public function __construct(string $guard, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (!$message) {
            $message = "Unknown auth guard [$guard]";
        }
        parent::__construct($message, $code, $previous);
    }
}
