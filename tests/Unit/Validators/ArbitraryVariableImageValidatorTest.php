<?php

use TailwindMerge\Validators\ArbitraryValueValidator;
use TailwindMerge\Validators\ArbitraryVariableImageValidator;

test('is arbitrary variable image', function ($input, $output) {
    expect(ArbitraryVariableImageValidator::validate($input))->toBe($output);
})->with([
    ['(image:test)', true],
    ['(url:test)', true],
    ['(other:test)', false],
    ['(test)', false],
    ['image:test', false],
]);
