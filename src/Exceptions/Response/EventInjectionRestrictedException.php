<?php

namespace Dust\Exceptions\Response;

use Throwable;
use Dust\Base\Response;

class EventInjectionRestrictedException extends \Exception
{
    public function __construct(Response $response, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = $message ?: sprintf('Event injection is restricted on this response [%s]', get_class($response));
        parent::__construct($message, $code, $previous);
    }
}
