<?php

namespace Dust\Base;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Dust\Base\Contracts\ResponseInterface;
use Dust\Base\Contracts\RequestHandlerInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class Controller implements RequestHandlerInterface
{
    protected array $bindings = [];

    public function __construct(protected ResponseInterface $response, protected Request $request)
    {
    }

    public function __invoke(): mixed
    {
        if ($this->bindings) {
            $this->resolveBindings();
        }

        return $this->response->send(
            $this,
            $this->request,
        );
    }

    final protected function resolveBindings(): void
    {
        $routeParams = [];
        $bindings = array_reduce($this->bindings, function ($list, $binding) {
            $list[class_basename($binding)] = $binding;

            return $list;
        }, []);
        foreach ($this->request->route()->parameters as $key => $value) {
            if (isset($bindings[Str::studly($key)])) {
                $this->resolve($bindings[Str::studly($key)], $key, $value);
            }
        }
    }

    final public function resolve(string $class, string $key, mixed $value): void
    {
        $instance = new $class;
        $record = $class::where($instance->getRouteKeyName(), $value)->first();
        if (! $record) {
            $e = new ModelNotFoundException();
            $e->setModel($class, [$value]);
            throw $e;
        }

        $this->request->route()->setParameter($key, $record);
    }
}
