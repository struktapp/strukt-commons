<?php

/** Behavioral coverage for global path, registry, stack, JSON, and UUID helpers. */

use Strukt\Registry;
use Strukt\Stack;

test('global path, array, JSON, and UUID helpers work without sibling packages', function (): void {
    $data = [];
    attach('user.name', $data, 'Ada');

    expect(dot('user.name', $data))->toBe('Ada')
        ->and(detach('user.name', $data))->toBe('Ada')
        ->and(dot('user.name', $data))->toBeNull()
        ->and(level(['user' => ['name' => 'Ada']]))->toBe(['user.name' => 'Ada'])
        ->and(json(['ok' => true])->decode())->toBe(['ok' => true])
        ->and(json('{"ok":true}')->valid())->toBeTrue()
        ->and(config('app.name'))->toBe('payroll')
        ->and(strlen(uuid()->yield()))->toBe(36);
});

test('registry and stack state can be reset deterministically', function (): void {
    Registry::resetInstance();
    $registry = Registry::getInstance();
    $registry->set('feature.enabled', true);

    Stack::clear();
    Stack::limit(2);
    stack('first');
    stack('second');
    stack('third');

    expect($registry->get('feature.enabled'))->toBeTrue()
        ->and(Stack::get()->yield())->toBe(['second', 'third']);

    Stack::clear();
    Stack::limit(10);
    Registry::resetInstance();
});

test('inflection helpers cover common words', function (): void {
    expect(plural('story'))->toBe('stories')
        ->and(singular('stories'))->toBe('story');
});
