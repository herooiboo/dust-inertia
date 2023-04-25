<?php

namespace Dust\Support\Enum\Concerns;

use Dust\Support\Enum\Contracts\StringableInterface;

trait HasOptions
{
    public static function options(): array
    {
        return array_reduce(self::cases(), function ($options, StringableInterface $case) {
            $options[] = ['text' => $case->toString(), 'value' => $case->value];

            return $options;
        }, []);
    }

    public static function values(): array
    {
        return array_reduce(self::cases(), function ($options, StringableInterface $case) {
            $options[] = $case->value;

            return $options;
        }, []);
    }
}
