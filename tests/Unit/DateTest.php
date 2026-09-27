<?php

/** Behavioral coverage for date comparison, periods, and formatting helpers. */

use Strukt\Today;

beforeEach(function (): void {
    Today::reset();
});

afterEach(function (): void {
    Today::reset();
});

test('dates compare, clone, reset, and generate inclusive random values', function (): void {
    $start = when('2024-01-01 12:00:00');
    $end = when('2024-01-31 12:00:00');

    $random = $start->rand($end);
    expect($random->gte($start))->toBeTrue()
        ->and($random->lte($end))->toBeTrue()
        ->and($start->clone()->equals($start))->toBeTrue()
        ->and($start->clone('+1 day')->gt($start))->toBeTrue();

    $midnight = $end->clone();
    $midnight->reset();
    expect($midnight->format('H:i:s'))->toBe('00:00:00');

    $last = $start->clone();
    $last->last();
    expect($last->format('H:i:s'))->toBe('23:59:59');
});

test('simulated today enforces an inclusive period', function (): void {
    $start = new DateTime('1960-01-01');
    $end = new DateTime('1963-12-31 23:59:59');
    Today::makePeriod($start, $end);
    Today::reset(new DateTime('1960-03-23'));

    expect(today()->format('Y-m-d'))->toBe('1960-03-23')
        ->and(when()->same(today()))->toBeTrue()
        ->and(Today::withDate(new DateTime('1959-04-01'))->isValid())->toBeFalse()
        ->and(Today::withDate(new DateTime('1960-04-01'))->isValid())->toBeTrue();

    expect(fn () => when('2024-01-01'))->toThrow(OutOfBoundsException::class);
});

test('date formatting helpers return stable output', function (): void {
    $date = when('2024-01-15 10:20:30');

    expect(format('date', $date))->toBe('2024-01-15')
        ->and(format('datetime', $date))->toBe('2024-01-15 10:20:30')
        ->and(Strukt\DateTime::create('15/01/2024', 'd/m/Y')->__toString())->toBe('15/01/2024');
});
