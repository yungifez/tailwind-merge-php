<?php

namespace TailwindMerge\Validators;

use TailwindMerge\Support\Str;

/**
 * @internal
 */
class ArbitraryVariableValidator implements \TailwindMerge\Contracts\ValidatorContract
{
    public static function validate(string $value): bool
    {
        return Str::hasMatch("/^\((?:(\w[\w-]*):)?(.+)\)$/i", $value);
    }
}
