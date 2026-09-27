<?php

/** Behavioral coverage for token parsing, mutation, and serialization. */

test('token queries parse, update, and preserve the original token', function (): void {
    $query = token('user:ada|roles:admin,editor');

    expect($query->get('user'))->toBe('ada')
        ->and($query->get('roles'))->toBe(['admin', 'editor'])
        ->and($query->token())->toBe('user:ada|roles:admin,editor');

    $query->set('status', 'active')->remove('user');

    expect($query->yield())->toBe('roles:admin,editor|status:active')
        ->and($query->has('user'))->toBeFalse();
});

test('tokenization rejects object values', function (): void {
    expect(fn () => arr(['object' => new stdClass()])->tokenize())
        ->toThrow(InvalidArgumentException::class);
});
