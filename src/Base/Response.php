<?php

namespace Dust\Base;

use Closure;
use Throwable;
use Dust\Support\Logger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Dust\Http\Responses\ErrorResponse;
use Illuminate\Foundation\Application;
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

    public function __construct(protected Application $app)
    {
    }

    /**
     * @throws Throwable
     */
    public function send(RequestHandlerInterface $handler, Request $request): mixed
    {
        try {
            $this->request = $request;
            $data = call_user_func([$handler, 'handle'], $this, $request);
            $this->fireSuccessChain($data);

            return $this->createResource($data);
        } catch (Throwable $e) {
            $handled = $this->isLaravelHandledException($e);
            if (! $handled) {
                $this->logError($handler, $request, $e);
            }

            $this->fireFailureChain($request, $e);

            return $this->error($request, $e, $handled);
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
                    $this->getClassName($handler),
                ),
            ),
            $e,
            $this->buildLogBody($request, $e),
        );
    }

    final protected function getSnakedName(string $name): string
    {
        return strtoupper(
            implode('_',
                array_filter(
                    preg_split('/(?=[A-Z])/', $name),
                ),
            ),
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
            ($onLog = $this->onLogObserver) ? $onLog($request, $e) : $this->errorMeta($e, self::isDebug()),
            ['user' => $request->user()->id ?? null],
        );
    }

    /**
     * @throws Throwable
     */
    final protected function error(Request $request, Throwable $e, bool $handled = false): JsonResponse|View
    {
        return $this->handleErrorResponse($e) ?: $this->defaultErrorResponse($request, $e, $handled);
    }

    /**
     * @throws Throwable
     */
    final protected function defaultErrorResponse(Request $request, Throwable $e, bool $handled): JsonResponse|ErrorResponse|View
    {
        if ($handled) {
            throw $e;
        }

        if( !$request->expectsJson() && view()->exists(config('dust.default_error_view')) ) {
            return view(config('dust.default_error_view'), ['exception' => $e]);
        }

        $debugMode = self::isDebug();
        return new ErrorResponse($debugMode ? $e->getMessage() : 'Error!! try again later.', $this->errorMeta($e, $debugMode), status: 500);
    }

    final protected function isLaravelHandledException(Throwable $e): bool
    {
        foreach (self::LARAVEL_HANDLED_EXCEPTIONS as $ex) {
            if ($e instanceof $ex) {
                return true;
            }
        }

        return false;
    }

    final protected function isDebug(): bool
    {
        return (bool) $this->app->config['app']['debug'];
    }

    protected function errorMeta(Throwable $e, bool $debugMode): array
    {
        if (! $debugMode) {
            return [];
        }

        return [
            'exception' => [
                'class' => get_class($e),
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
        ];
    }

    protected function success(mixed $resource): void
    {
        //
    }

    protected function failure(Request $request, ?Throwable $e): void
    {
        //
    }

    protected function handleErrorResponse(Throwable $e): false|JsonResponse|View
    {
        return false;
    }

    abstract protected function createResource(mixed $resource): mixed;
}
