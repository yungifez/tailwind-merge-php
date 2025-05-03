<?php

use TailwindMerge\Validators\ArbitraryVariableShadowValidator;

test('is arbitrary variable shadow', function ($input, $output) {
    expect(ArbitraryVariableShadowValidator::validate($input))->toBe($output);
})->with([
    ['(shadow:test)', true],
    ['(test)', true],
    ['(other:test)', false],
    ['shadow:test', false],
]);
