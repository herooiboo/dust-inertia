<?php

namespace Dust\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * @mixin Eloquent
 * @mixin Builder
 */
abstract class Repository
{
    public function __construct(protected Eloquent $model)
    {
    }

    public function update(int|string $id, array $payload): ?Eloquent
    {
        $model = $this->model
            ->query()
            ->find($id);
        if (! $model) {
            return null;
        }

        $model->update($payload);

        return $model;
    }

    public function delete(int|string $id): bool
    {
        return $this->model
            ->query()
            ->where('id', $id)
            ->delete();
    }

    public function __get(string $name)
    {
        return $this->model->{$name};
    }

    public function __set(string $name, $value): void
    {
        $this->model->{$name} = $value;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->model->{$name}(...$arguments);
    }

    public function model(): Eloquent
    {
        return $this->model;
    }

    public function setModel(Eloquent $model): static
    {
        $this->model = $model;

        return $this;
    }
}
