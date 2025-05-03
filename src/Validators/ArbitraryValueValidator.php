<?php

namespace TailwindMerge\Validators;

use TailwindMerge\Support\Str;

/**
 * @internal
 */
class ArbitraryValueValidator implements \TailwindMerge\Contracts\ValidatorContract
{
    final public const ARBITRARY_VALUE_REGEX = '/^\[(?:(\w[\w-]*):)?(.+)\]$/i';

    public static function validate(string $value): bool
    {
        return Str::hasMatch(self::ARBITRARY_VALUE_REGEX, $value);
    }
}
