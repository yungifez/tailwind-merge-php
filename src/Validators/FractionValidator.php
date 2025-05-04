<?php

namespace TailwindMerge\Validators;

/**
 * @internal
 */
class FractionValidator implements \TailwindMerge\Contracts\ValidatorContract
{
    public static function validate(string $value): bool
    {
        return preg_match("/^\d+\/\d+$/", $value) == true;
    }
}
