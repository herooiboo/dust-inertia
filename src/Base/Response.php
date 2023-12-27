<?php

namespace Dust\Base;

use Closure;
use Throwable;
use Dust\Support\Logger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Dust\Http\Responses\ErrorResponse;
use Dust\Base\Contracts\ResponseInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Dust\Base\Contracts\RequestHandlerInterface;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\MultipleRecordsFoundException;
use Dust\Base\Contracts\RestrictEventInjectionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Dust\Exceptions\Response\EventInjectionRestrictedException;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

abstract class Response implements ResponseInterface
{
    protected const LARAVEL_HANDLED_EXCEPTIONS = [
        AuthenticationException::class,
        AuthorizationException::class,
        BackedEnumCaseNotFoundException::class,
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        MultipleRecordsFoundException::class,
        RecordsNotFoundException::class,
        SuspiciousOperationException::class,
        TokenMismatchException::class,
        ValidationException::class,
    ];

    private ?Closure $onLogObserver = null;

    /**
     * @var callable[]
     */
    private array $onSuccessHandlers = [];

    /**
     * @var callable[]
     */
    private array $onFailureHandlers = [];

    private bool $silence = false;

    /**
     * @throws Throwable
     */
    public function send(RequestHandlerInterface $handler, Request $request): mixed
    {
        try {
            $data = call_user_func([$handler, 'handle'], $this, $request);
            $this->fireSuccessChain($data);

            return $this->createResource($data);
        } catch (Throwable $e) {
            $handled = $this->isLaravelHandledException($e);
            if (! $handled) {
                $this->logError($handler, $request, $e);
            }

            $this->fireFailureChain($request, $e);

            return $this->error($e, $handled);
        }
    }

    final public function silent(): static
    {
        $this->silence = true;

        return $this;
    }

    /**
     * @throws \Dust\Exceptions\Response\EventInjectionRestrictedException
     */
    final public function onSuccess(callable $handler): static
    {
        if ($this instanceof RestrictEventInjectionInterface) {
            throw new EventInjectionRestrictedException($this);
        }
        $this->onSuccessHandlers[] = $handler;

        return $this;
    }

    /**
     * @throws \Dust\Exceptions\Response\EventInjectionRestrictedException
     */
    final public function onFailure(callable $handler): static
    {
        if ($this instanceof RestrictEventInjectionInterface) {
            throw new EventInjectionRestrictedException($this);
        }

        $this->onFailureHandlers[] = $handler;

        return $this;
    }

    final public function onLog(Closure $handler): static
    {
        $this->onLogObserver = $handler;

        return $this;
    }

    final protected function logError(RequestHandlerInterface $handler, Request $request, Throwable $e): void
    {
        Logger::error(
            sprintf('%s_ERROR',
                $this->getSnakedName(
                    $this->getClassName($handler)
                )
            ),
            $e,
            $this->buildLogBody($request, $e)
        );
    }

    final protected function getSnakedName(string $name): string
    {
        return strtoupper(
            implode('_',
                array_filter(
                    preg_split('/(?=[A-Z])/', $name)
                )
            )
        );
    }

    final protected function getClassName(RequestHandlerInterface $handler): string
    {
        $namespace = explode('\\', get_class($handler));

        return array_pop($namespace);
    }

    final protected function fireSuccessChain(mixed $resource): void
    {
        if ($this->silence) {
            return;
        }

        foreach ($this->onSuccessHandlers as $handler) {
            $handler($resource);
        }

        $this->success($resource);
    }

    final protected function fireFailureChain(Request $request, ?Throwable $e = null): void
    {
        if ($this->silence) {
            return;
        }

        foreach ($this->onFailureHandlers as $handler) {
            $handler($request, $e);
        }

        $this->failure($request, $e);
    }

    final protected function buildLogBody(Request $request, Throwable $e): array
    {
        return array_merge(
            ($onLog = $this->onLogObserver) ? $onLog($request, $e) : $this->errorMeta(),
            ['user' => $request->user()->id ?? null]
        );
    }

    /**
     * @throws Throwable
     */
    final protected function error(Throwable $e, bool $handled = false): JsonResponse
    {
        return $this->handleErrorResponse($e) ?: $this->defaultErrorResponse($e, $handled);
    }

    /**
     * @throws Throwable
     */
    final protected function defaultErrorResponse(Throwable $e, bool $handled): JsonResponse|ErrorResponse
    {
        if ($handled) {
            throw $e;
        }

        return new ErrorResponse('Error!! try again later.', $this->errorMeta(), status: 500);
    }

    public function isLaravelHandledException(Throwable $e): bool
    {
        foreach (self::LARAVEL_HANDLED_EXCEPTIONS as $ex) {
            if ($e instanceof $ex) {
                return true;
            }
        }

        return false;
    }

    public function errorMeta(): array
    {
        return [];
    }

    protected function success(mixed $resource): void
    {
        //
    }

    protected function failure(Request $request, ?Throwable $e): void
    {
        //
    }

    protected function handleErrorResponse(Throwable $e): false|JsonResponse
    {
        return false;
    }

    abstract protected function createResource(mixed $resource): mixed;
}
