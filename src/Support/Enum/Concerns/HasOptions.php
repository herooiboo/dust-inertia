<?php

namespace Dust\Support\Enum\Concerns;

use UnitEnum;
use BackedEnum;
use Dust\Support\Enum\Contracts\StringableInterface;

/**
 * @implements UnitEnum
 */
trait HasOptions
{
    public static function options(): array
    {
        return array_reduce(self::cases(), function ($options, StringableInterface|UnitEnum $case) {
            $options[] = ['text'  => $case instanceof StringableInterface ? $case->toString() : $case->name,
                          'value' => static::getValue($case)
            ];

            return $options;
        }, []);
    }

    public static function values(): array
    {
        return array_reduce(self::cases(), function ($options, StringableInterface|UnitEnum $case) {
            $options[] = static::getValue($case);

            return $options;
        }, []);
    }

    public static function getValue(UnitEnum|StringableInterface $case): string
    {
        if ($case instanceof BackedEnum) {
            return $case->value ?: $case->name;
        }

        return $case->name;
    }
}
