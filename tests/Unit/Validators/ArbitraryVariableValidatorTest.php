<?php

use TailwindMerge\Validators\ArbitraryVariableValidator;

test('is arbitrary variable', function ($input, $output) {
    expect(ArbitraryVariableValidator::validate($input))->toBe($output);
})->with([
    ['(1)', true],
    ['(bla)', true],
    ['(not-an-arbitrary-value?)', true],
    ['(--my-arbitrary-variable)', true],
    ['(label:--my-arbitrary-variable)', true],
    ['()', false],
    ['(1', false],
    ['1)', false],
    ['1', false],
    ['one', false],
    ['o(n)e', false],
]);
