<?php

use TailwindMerge\Validators\ArbitraryVariablePositionValidator;

test('is arbitrary variable position', function ($input, $output) {
    expect(ArbitraryVariablePositionValidator::validate($input))->toBe($output);
})->with([
    ['(position:test)', true],
    ['(other:test)', false],
    ['(test)', false],
    ['position:test', false],
]);
