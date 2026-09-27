Strukt Commons
==============

[![Build Status](https://travis-ci.org/pitsolu/strukt-commons.svg?branch=master)](https://packagist.org/packages/strukt/commons)
[![Latest Stable Version](https://poser.pugx.org/strukt/commons/v/stable)](https://packagist.org/packages/strukt/commons)
[![Total Downloads](https://poser.pugx.org/strukt/commons/downloads)](https://packagist.org/packages/strukt/commons)
[![Latest Unstable Version](https://poser.pugx.org/strukt/commons/v/unstable)](https://packagist.org/packages/strukt/commons)
[![License](https://poser.pugx.org/strukt/commons/license)](https://packagist.org/packages/strukt/commons)

Strukt Commons is a small, self-contained PHP utility library. It provides
chainable array and string values, dot-separated collections, date ranges,
token queries, registries, message stacks, JSON helpers, and common global
functions.

The package requires PHP 8.2 or newer and has no runtime dependencies. Optional
integrations such as Ramsey UUID and Symfony's English inflector are used when
they are already installed, while standalone fallbacks remain available.

## Installation

Install the package through Composer:

```sh
composer require strukt/commons
```

When working from this repository, install the development dependencies and
run the test suite:

```sh
composer install
composer test
```

Composer loads the `Strukt\` namespace from `src/Strukt` and registers the
global helpers from `src/helpers.php`.

```php
require __DIR__ . '/vendor/autoload.php';
```

## Usage

### Collection

`collect()` creates a mutable collection whose values can be read and written
through dot-separated paths.

```php
$contact = collect([]);
$contact->set('mobile', '+2540770123456');
$contact->set('work-phone', '+2540202345678');

$user = collect([]);
$user->set('contacts', $contact->yield());

echo $user->get('contacts.mobile');
// +2540770123456

$user->exists('contacts.mobile');
// true

$user->remove('contacts.work-phone');
$user->keys('contacts');
// ['mobile']
```

The same dot-path behavior is available through the `dot()`, `attach()`, and
`detach()` helpers:

```php
$data = [
    'user' => [
        'firstname' => 'Gene',
        'surname' => 'Wilder',
        'db' => [
            'config' => [
                'username' => 'root',
                'password' => '_root!',
            ],
        ],
    ],
];

echo dot('user.db.config.username', $data);
// root

attach('user.db.config.host', $data, 'localhost');
detach('user.db.config.password', $data);
```

### Value Objects

Value objects expose their raw value through `yield()`. Most transformations
return a new wrapper so calls can be chained without changing the original.

```php
$name = str('user name')->toSnake();
echo $name->yield();
// user_name

$items = arr(['first', 'second'])->push('third');
print_r($items->yield());
```

## Value Objects

### DateTime

`when()` creates a `Strukt\DateTime` value. It accepts normal PHP date strings,
relative expressions, and Unix timestamps.

```php
$start = when();
$end = when('+30 days');
$random = $start->rand($end);

echo $random;

$end->gt($start);
// true

$start->btwn($start, $end);
// true

$start->reset();
$start->last();
echo $start;
```

Date values also support `gte()`, `gt()`, `lte()`, `lt()`, `equals()`,
`same()`, `btwn()`, `clone()`, and the relative `when()` description. `same()`
compares calendar dates, while `equals()` compares the complete instant.

### Today (Date Influence)

`period()` controls an inclusive date range and can provide a simulated current
day. This is useful for deterministic application behavior and tests.

```php
$period = period();
$period->create(when('1900-01-01'), when('1963-12-31'));
$period->reset(when('1960-03-23'));

$fakeToday = today();
echo $fakeToday->format('Y-m-d');
// 1960-03-23

\Strukt\Today::withDate(when('1959-01-01'))->isValid();
// true

$period->reset();
```

`today()` follows the simulated day until the period controller is reset.
`Strukt\Today::makePeriod()`, `Strukt\Today::reset()`,
`Strukt\Today::hasPeriod()`, and `Strukt\Today::getState()` are available
when direct static access is preferred.

### String

`str()` returns a chainable `Strukt\Str` wrapper for common byte-oriented
string operations.

```php
$value = str('Strukt Commons');

echo $value->toSnake();
// strukt_commons

echo $value->toCamel();
// StruktCommons

$value->startsWith('Strukt');
// true

$value->replace('Commons', 'Library');
// Strukt Library

str('[commons]')->btwn('[', ']');
// commons

str('log')->pad('.')->right(3);
// log...

str('admin@example.com')->is()->email();
// true
```

Other string operations include `prepend()`, `concat()`, `len()`, `count()`,
`split()`, `slice()`, `toUpper()`, `toLower()`, `endsWith()`, `contains()`,
`replaceAt()`, `replaceFirst()`, `replaceLast()`, `first()`, `last()`,
`part()`, `repeat()`, and `newline()`.

### Array

`arr()` returns a chainable `Strukt\Arr` wrapper. It implements both
`Countable` and `IteratorAggregate` and supports list and associative arrays.

```php
$numbers = arr([1, 2, 2, 3]);

echo $numbers->sum();
// 8

echo $numbers->product();
// 12

print_r($numbers->uniq()->values()->yield());
// [1, 2, 3]

print_r($numbers->distinct()->yield());
// [1 => 1, 2 => 2, 3 => 1]
```

Array transformations can be composed:

```php
$rows = arr([
    ['name' => 'Ada', 'score' => 92],
    ['name' => 'Grace', 'score' => 88],
]);

$names = $rows->column('name')->yield();
$ordered = $rows->order()->desc('score')->yield();
$flat = arr(['user' => ['name' => 'Ada']])->level()->yield();

$upper = arr(['ada', 'grace'])
    ->map(fn (int|string $key, string $value): string => strtoupper($value))
    ->yield();
```

Useful predicates and selectors include:

```php
arr(['ada', 'grace'])->isof()->strings();
// true

arr([1, 1])->are()->all(1);
// true

arr(['name' => 'Ada'])->is()->map();
// true

arr([1, 2, 3])->only([1, 3])->yield();
// [1, 3]

array_values(arr([3, 1, 2])->sort()->asc());
// [1, 2, 3]
```

The wrapper also provides `push()`, `enqueue()`, `prequeue()`, `remove()`,
`merge()`, `enjoin()`, `slice()`, `reverse()`, `flip()`, `rehash()`, `map()`,
`filter()`, `each()`, `diff()`, `cross()`, `join()`, `contains()`, `has()`,
`skip()`, `jump()`, `stopAt()`, `will()`, and pointer helpers such as
`first()`, `current()`, `next()`, `sibling()`, and `last()`.

Nested arrays can be flattened with optional prefixes:

```php
$flat = arr([
    'user' => [
        'name' => 'Ada',
        'roles' => ['admin', 'author'],
    ],
])->level()->prefix('payload')->yield();

// [
//     'payload.user.name' => 'Ada',
//     'payload.user.roles.0' => 'admin',
//     'payload.user.roles.1' => 'author',
// ]
```

## Others

### Token Query

Token queries read and write compact `key:value|key:value` strings.

```php
$query = token('page:2|tag:php,composer');

$query->has('page');
// true

$query->get('tag');
// ['php', 'composer']

$query->set('sort', 'name')->remove('page');
echo $query->yield();
// tag:php,composer|sort:name
```

Use `tokenize()` when an associative array should be serialized directly:

```php
echo tokenize([
    'page' => 2,
    'tag' => ['php', 'composer'],
]);
// page:2|tag:php,composer
```

### Stack / Messages

`stack()` records messages in a bounded, process-local stack. The default limit
is ten messages.

```php
\Strukt\Stack::clear();

$message = stack('User was not found');
$message->add('A retry was scheduled');

$messages = \Strukt\Stack::get();
$messages->yield();

$filtered = \Strukt\Stack::get('/retry/');
$filtered->yield();

\Strukt\Stack::limit(20);
```

`Strukt\Stack::clear()` removes all messages, and `Strukt\Stack::get()` returns an `Arr`
wrapper so the normal array operations remain available.

### Json

`json()` provides a small wrapper around native JSON encoding and decoding.

```php
$payload = json([
    'name' => 'Peter',
    'active' => true,
]);

echo $payload->pp();

$encoded = $payload->encode();
$decoded = json($encoded)->decode();

json($encoded)->valid();
// true
```

Encoding and decoding use `JSON_THROW_ON_ERROR`, so malformed JSON and values
that cannot be encoded raise `JsonException`.

### Registry, Configuration, and Helpers

The process-local registry stores dot-separated values and is available through
`reg()` or `Registry`:

```php
reg('user.name', 'Ada');
echo reg('user.name');
// Ada

echo config('app.name');
// payroll

echo format('date', when('2024-01-01'));
// 2024-01-01

echo plural('story');
// stories

echo singular('stories');
// story

echo uuid();
```

Additional helpers include `alias()` for class aliases, `app()` for resolving
registered implementations, `provider()` for invoking provider registration,
`is_map()`, `notnull()`, `negate()`, and `raise()`.

## Development

Run the Pest test suite with:

```sh
composer test
```

Coverage is available when the local PHP coverage driver is installed:

```sh
composer test:coverage
```
