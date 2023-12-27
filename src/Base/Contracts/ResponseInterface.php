<?php

namespace Dust\Base\Contracts;

use Closure;
use Illuminate\Http\Request;

interface ResponseInterface
{
    public function send(RequestHandlerInterface $handler, Request $request): mixed;

    public function silent(): static;

    /**
     * @throws \Dust\Exceptions\Response\EventInjectionRestrictedException
     */
    public function onSuccess(callable $handler): static;

    /**
     * @throws \Dust\Exceptions\Response\EventInjectionRestrictedException
     */
    public function onFailure(callable $handler): static;

    public function onLog(Closure $handler): static;
}
