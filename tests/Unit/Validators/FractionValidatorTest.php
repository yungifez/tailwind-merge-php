<?php

use TailwindMerge\Validators\FractionValidator;
use TailwindMerge\Validators\IntegerValidator;

test('is fraction', function ($input, $output) {
    expect(FractionValidator::validate($input))->toBe($output);
})->with([
    ['1/2', true],
    ['123/209', true],
    ['1', false],
    ['1/2/3', false],
    ['[1/2]', false],
]);
