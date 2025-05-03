<?php

use TailwindMerge\Validators\ArbitraryVariableShadowValidator;
use TailwindMerge\Validators\ArbitraryVariableSizeValidator;

test('is arbitrary variable size', function ($input, $output) {
    expect(ArbitraryVariableSizeValidator::validate($input))->toBe($output);
})->with([
    ['(size:test)', true],
    ['(length:test)', true],
    ['(percentage:test)', true],
    ['(test)', false],
    ['(other:test)', false],
    ['size:test', false],
]);
