<?php

use TailwindMerge\Validators\ArbitraryVariableFamilyNameValidator;

test('is arbitrary variable family name', function ($input, $output) {
    expect(ArbitraryVariableFamilyNameValidator::validate($input))->toBe($output);
})->with([
    ['(family-name:test)', true],
    ['(other:test)', false],
    ['(test)', false],
    ['family-name:test', false],
]);
