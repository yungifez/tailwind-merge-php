<?php

use TailwindMerge\Validators\ArbitraryValueValidator;
use TailwindMerge\Validators\ArbitraryVariableImageValidator;
use TailwindMerge\Validators\ArbitraryVariablePositionValidator;

test('is arbitrary variable position', function ($input, $output) {
    expect(ArbitraryVariablePositionValidator::validate($input))->toBe($output);
})->with([
    ['(position:test)', true],
    ['(other:test)', false],
    ['(test)', false],
    ['position:test', false],
]);
