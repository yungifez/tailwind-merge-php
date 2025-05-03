<?php

namespace TailwindMerge\Validators;

use TailwindMerge\Support\Str;
use TailwindMerge\Validators\Concerns\ValidatesArbitraryValue;


/**
 * @internal
 */
class AnyNonArbitraryValidator implements \TailwindMerge\Contracts\ValidatorContract
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return !Str::hasMatch('/^\[(?:(\w[\w-]*):)?(.+)\]$/i', $value) && !Str::hasMatch("/^\((?:(\w[\w-]*):)?(.+)\)$/i", $value);
;
    }
}
