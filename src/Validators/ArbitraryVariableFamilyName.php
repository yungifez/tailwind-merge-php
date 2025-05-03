<?php

namespace TailwindMerge\Validators;

use TailwindMerge\Support\Str;
use TailwindMerge\Validators\Concerns\ValidatesArbitraryValue;


/**
 * @internal
 */
class ArbitraryVariableFamilyNameValidator implements \TailwindMerge\Contracts\ValidatorContract
{
    use ValidatesArbitraryValue;
    public static function validate(string $value): bool
    {
        return self::getIsArbitraryVariable($value, 'family-name', fn (): bool => false);
    }
}
