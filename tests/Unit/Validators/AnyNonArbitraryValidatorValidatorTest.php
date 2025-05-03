<?php

use TailwindMerge\Validators\AnyNonArbitraryValidator;
use TailwindMerge\Validators\AnyValueValidator;

test('is any non arbitrary value', function ($input, $output) {
    expect(AnyNonArbitraryValidator::validate($input))->toBe($output);
})->with([
    ['test', true],
    ['1234-hello-world', true],
    ['[hello', true],
    ['[)', true],
    ['hello]', true],
    ['[test]', false],
    ['[label:test]', false],
    ['(test)', false],
    ['(label:test)', false],
]);
