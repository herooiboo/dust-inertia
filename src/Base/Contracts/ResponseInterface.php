<?php

namespace Dust\Base\Contracts;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ResponseInterface
{
    public function send(RequestHandlerInterface $handler, Request $request): JsonResponse|JsonResource|LengthAwarePaginator|StreamedResponse|RedirectResponse;

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
