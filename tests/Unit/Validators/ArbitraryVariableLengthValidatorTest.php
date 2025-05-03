<?php

use TailwindMerge\Validators\ArbitraryValueValidator;
use TailwindMerge\Validators\ArbitraryVariableImageValidator;
use TailwindMerge\Validators\ArbitraryVariableLengthValidator;

test('is arbitrary variable length', function ($input, $output) {
    expect(ArbitraryVariableLengthValidator::validate($input))->toBe($output);
})->with([
    ['(length:test)', true],
    ['(other:test)', false],
    ['(test)', false],
    ['length:test', false],
]);
