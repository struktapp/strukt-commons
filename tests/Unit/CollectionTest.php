<?php

/** Behavioral coverage for dot-path collection reads and writes. */

test('collections read, write, list, and remove dot paths', function (): void {
    $collection = collect(['user' => ['name' => 'Ada', 'active' => null]]);

    expect($collection->get('user.name'))->toBe('Ada')
        ->and($collection->exists('user.active'))->toBeTrue()
        ->and($collection->exists('user.missing'))->toBeFalse();

    $collection->set('user.role', 'admin');
    expect($collection->keys('user'))->toBe(['name', 'active', 'role'])
        ->and($collection->remove('user.name'))->toBe('Ada')
        ->and($collection->exists('user.name'))->toBeFalse();
});

test('collections replace scalar parents when attaching deeper paths', function (): void {
    $collection = collect(['user' => 'anonymous']);

    $collection->set('user.name', 'Ada');

    expect($collection->get('user.name'))->toBe('Ada')
        ->and($collection->ask('user'))->toBeInstanceOf(Strukt\Collection::class);
});
