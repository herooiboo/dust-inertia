<?php

namespace Dust\Support\Enum\Concerns;

use UnitEnum;
use Dust\Support\Enum\Contracts\StringableInterface;

/**
 * @implements UnitEnum
 *
 * @method static array cases
 */
trait HasOptions
{
    public static function options(): array
    {
        return array_reduce(self::cases(), function ($options, StringableInterface|UnitEnum $case) {
            $options[] = ['text' => $case->toString(), 'value' => $case->value ?: $case->name];

            return $options;
        }, []);
    }

    public static function values(): array
    {
        return array_reduce(self::cases(), function ($options, StringableInterface|UnitEnum $case) {
            $options[] = $case->value ?: $case->name;

            return $options;
        }, []);
    }
}
