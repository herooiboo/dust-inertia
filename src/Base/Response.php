<?php

namespace Dust\Base;

use Closure;
use Throwable;
use Dust\Support\Logger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Dust\Http\Responses\ErrorResponse;
use Dust\Base\Contracts\ResponseInterface;
use Illuminate\Auth\AuthenticationException;
use Dust\Base\Contracts\RequestHandlerInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Dust\Base\Contracts\RestrictEventInjectionInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Dust\Exceptions\Response\EventInjectionRestrictedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class Response implements ResponseInterface
{
    private Closure|null $onLogObserver = null;

    /**
     * @var callable[]
     */
    private array $onSuccessHandlers = [];

    /**
     * @var callable[]
     */
    private array $onFailureHandlers = [];

    private bool $silence = false;

    public function send(RequestHandlerInterface $handler, Request $request): mixed
    {
        try {
            $data = call_user_func([$handler, 'handle'], $this, $request);
            $this->fireSuccessChain($data);

            return $this->createResource($data);
        } catch (UnauthorizedHttpException|AuthenticationException $e) {
            $this->fireFailureChain($request, $e);

            return $this->error($e);
        } catch (Throwable $e) {
            $this->logError($handler, $request, $e);
            $this->fireFailureChain($request, $e);

            return $this->error($e);
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

    final protected function fireFailureChain(Request $request, Throwable $e = null): void
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
            ($onLog = $this->onLogObserver) ? $onLog($request, $e) : [],
            ['user' => $request->user()->id ?? null]
        );
    }

    final protected function error(Throwable $e): JsonResponse
    {
        return $this->handleErrorResponse($e) ?: new ErrorResponse('Error!! try again later.', [], status: 500);
    }

    protected function success(mixed $resource): void
    {
        //
    }

    protected function failure(Request $request, Throwable|null $e): void
    {
        //
    }

    protected function handleErrorResponse(Throwable $e): bool|JsonResponse
    {
        return false;
    }

    abstract protected function createResource(mixed $resource): mixed;
}
