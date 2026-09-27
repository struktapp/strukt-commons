<?php

/** Behavioral coverage for the chainable array wrapper and helper objects. */

test('array wrappers transform values without changing the source', function (): void {
    $source = arr(['first' => 'Ada', 'second' => 'Grace']);

    expect($source->map(fn ($key, $value) => strtoupper($value))->yield())
        ->toBe(['first' => 'ADA', 'second' => 'GRACE'])
        ->and($source->yield())
        ->toBe(['first' => 'Ada', 'second' => 'Grace']);
});

test('array wrappers support mutation, filtering, and iteration', function (): void {
    $values = arr(['a', null, 'b', '']);

    expect($values->filter()->yield())->toBe(['a', 'b']);
    expect($values->push('c')->last())->toBe('c');
    expect($values->dequeue())->toBe('a');
    expect($values->pop())->toBe('');

    $iterator = arr(['a', null, 'b']);
    expect($iterator->current())->toBe('a')
        ->and($iterator->next())->toBeTrue()
        ->and($iterator->current())->toBeNull();
});

test('array traversal rules are consumed by each', function (): void {
    $values = arr(['keep' => 'A', 'skip' => 'B', 'jump' => 'C', 'stop' => 'D', 'after' => 'E']);

    $result = $values
        ->skip('skip')
        ->jump('C')
        ->stopAt('stop')
        ->each(fn ($key, $value) => $value . '!');

    expect($result->yield())->toBe([
        'keep' => 'A!',
        'skip' => 'B',
        'jump' => 'C',
        'stop' => 'D',
        'after' => 'E',
    ]);
});

test('array wrappers flatten, compare, and order nested rows', function (): void {
    $nested = arr(['user' => ['name' => 'Ada', 'roles' => ['admin']]]);

    expect($nested->level()->yield())->toBe([
        'user.name' => 'Ada',
        'user.roles.0' => 'admin',
    ]);

    $rows = arr([
        ['name' => 'Grace', 'age' => 37],
        ['name' => 'Ada', 'age' => 36],
    ]);

    expect($rows->column('name')->yield())->toBe(['Grace', 'Ada']);
    expect($rows->order()->asc('age')->yield())->toBe([
        ['name' => 'Ada', 'age' => 36],
        ['name' => 'Grace', 'age' => 37],
    ]);

    expect(arr(['a' => 1, 'b' => 2])->diff(['a' => 1])->keys()->yield())
        ->toBe(['b' => 2]);
    expect(arr(['a' => 1, 'b' => 2])->cross(['a' => 1])->map()->yield())
        ->toBe(['a' => 1]);
});

test('array wrappers validate aggregate operations', function (): void {
    expect(arr([1, 2, 3])->sum())->toBe(6)
        ->and(arr([2, 3, 4])->product())->toBe(24)
        ->and(arr(['a', 'b'])->join(','))->toBe('a,b')
        ->and(arr(['a', 'a', 'b'])->distinct()->yield())->toBe(['a' => 2, 'b' => 1]);

    expect(fn () => arr([1, ['nested']])->sum())
        ->toThrow(InvalidArgumentException::class);
});

test('array wrappers tokenize associative values', function (): void {
    $values = arr([
        'user' => 'ada',
        'roles' => ['admin', 'editor'],
    ]);

    expect($values->tokenize())->toBe('user:ada|roles:admin,editor')
        ->and($values->contains(['admin', 'editor']))->toBeTrue();
});
