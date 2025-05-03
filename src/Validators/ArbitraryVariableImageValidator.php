<?php

namespace TailwindMerge\Validators;

use TailwindMerge\Validators\Concerns\ValidatesArbitraryValue;

/**
 * @internal
 */
class ArbitraryVariableImageValidator implements \TailwindMerge\Contracts\ValidatorContract
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryVariable($value, ['image', 'url']);
    }
}
