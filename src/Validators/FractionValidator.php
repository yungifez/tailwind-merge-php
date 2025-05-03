<?php

namespace TailwindMerge\Validators;

use TailwindMerge\Validators\Concerns\ValidatesArbitraryValue;

/**
 * @internal
 */
class FractionValidator implements \TailwindMerge\Contracts\ValidatorContract
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return preg_match("/^\d+\/\d+$/", $value);
    }
}
