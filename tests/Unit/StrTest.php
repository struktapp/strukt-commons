<?php

/** Behavioral coverage for the chainable string wrapper and its validators. */

test('string wrappers inspect and transform text', function (): void {
    $value = str('Strukt Framework');

    expect($value->startsWith('Strukt'))->toBeTrue()
        ->and($value->endsWith('Framework'))->toBeTrue()
        ->and($value->contains('Frame'))->toBeTrue()
        ->and($value->first(3)->yield())->toBe('Str')
        ->and($value->last(4)->yield())->toBe('work')
        ->and($value->slice(7, 5)->yield())->toBe('Frame');
});

test('string wrappers replace and extract text', function (): void {
    $value = str('{bold}Black Beauty{/bold}');

    expect($value->btwn('{bold}', '{/bold}')->yield())->toBe('Black Beauty')
        ->and(str('Blah blah blah!')->replaceFirst('blah', 'yaba daba')->yield())
        ->toBe('Blah yaba daba blah!')
        ->and(str('Blah blah blah!')->replaceLast('blah', 'doo')->yield())
        ->toBe('Blah blah doo!')
        ->and(str('Strukt Framework')->replaceAt('ing', 3, 3)->yield())
        ->toBe('String Framework');
});

test('string wrappers convert case and pad blocks', function (): void {
    expect(str('thisIsCamelCase')->toSnake()->yield())->toBe('this_is_camel_case')
        ->and(str('this_is_camel_case')->toCamel()->yield())->toBe('ThisIsCamelCase')
        ->and(str('x')->pad('-')->both(2))->toBe('-x-')
        ->and(str("a\nb")->pad(' ')->block()->left(2))->toBe("  a\n  b");
});

test('string wrappers validate and cast naturally', function (): void {
    expect(str(' person@example.com ')->is()->email())->toBeTrue()
        ->and(str(' ')->empty())->toBeTrue()
        ->and(str('/^[a-z]+$/')->isRegEx(''))->toBeFalse()
        ->and((string) str('hello'))->toBe('hello');
});
