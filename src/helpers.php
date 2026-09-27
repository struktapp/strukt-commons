<?php

/**
 * Global compatibility helpers for the Strukt Commons utility classes.
 *
 * Functions are registered conditionally so the package can coexist with
 * older Strukt packages that provide the same helper names.
 */

use Strukt\Arr;
use Strukt\Collection;
use Strukt\DateTime as StruktDateTime;
use Strukt\Registry;
use Strukt\Stack;
use Strukt\Str;
use Strukt\Today;
use Strukt\TokenQuery;

if (!function_exists('is_map')) {
    /**
     * Checks whether an array is a non-empty associative map.
     *
     * @param array<int|string, mixed> $value Array to inspect.
     * @return bool True when the array is map-shaped.
     */
    function is_map(array $value): bool
    {
        return $value !== [] && !array_is_list($value);
    }
}

if (!function_exists('notnull')) {
    /**
     * Checks whether a value is not null.
     *
     * @param mixed $value Value to inspect.
     * @return bool True when the value is not null.
     */
    function notnull(mixed $value): bool
    {
        return $value !== null;
    }
}

if (!function_exists('negate')) {
    /**
     * Negates a boolean value.
     *
     * @param bool $value Boolean to invert.
     * @return bool Inverted boolean.
     */
    function negate(bool $value): bool
    {
        return !$value;
    }
}

if (!function_exists('raise')) {
    /**
     * Throws a Strukt runtime exception.
     *
     * @param string $message Error message.
     * @param int $code Exception code.
     * @return Strukt\Raise Never returns because the exception is thrown.
     * @throws Strukt\Raise Always.
     */
    function raise(string $message, int $code = 500): Strukt\Raise
    {
        throw new Strukt\Raise($message, $code);
    }
}

if (!function_exists('helper_add')) {
    /** Compatibility no-op for packages that conditionally register helpers. */
    /**
     * Reports that a helper name can be registered.
     *
     * @param string $name Helper name.
     * @return bool Always true for compatibility.
     */
    function helper_add(string $name): bool
    {
        return true;
    }
}

if (!function_exists('dot')) {
    /**
     * Reads a dot-separated path from an array.
     *
     * @param string $path Path such as `user.profile.name`.
     * @param array<int|string, mixed> $map Array to inspect by reference.
     * @return mixed Value at the path, or `null` when absent.
     */
    function dot(string $path, array &$map): mixed
    {
        return Collection::dot($path, $map);
    }
}

if (!function_exists('attach')) {
    /**
     * Writes a value at a dot-separated path.
     *
     * @param string $path Path to write.
     * @param array<int|string, mixed> $map Array to modify by reference.
     * @param mixed $value Value to assign.
     * @return void
     */
    function attach(string $path, array &$map, mixed $value): void
    {
        Collection::attach($path, $map, $value);
    }
}

if (!function_exists('detach')) {
    /**
     * Removes and returns a value at a dot-separated path.
     *
     * @param string $path Path to remove.
     * @param array<int|string, mixed> $map Array to modify by reference.
     * @return mixed Removed value, or `null` when absent.
     */
    function detach(string $path, array &$map): mixed
    {
        return Collection::detach($path, $map);
    }
}

if (!function_exists('collect')) {
    /**
     * Wraps an array in a mutable collection.
     *
     * @param array<int|string, mixed> $value Initial collection values.
     * @return Collection New collection instance.
     */
    function collect(array $value): Collection
    {
        return new Collection($value);
    }
}

if (!function_exists('reg')) {
    /**
     * Reads or writes the process-local registry.
     *
     * A non-null second argument writes a value; omitting the key returns the
     * registry object itself.
     *
     * @param string|null $key Optional dot-separated registry key.
     * @param mixed $value Optional value to store.
     * @return mixed Registry object, stored value, or retrieved value.
     */
    function reg(?string $key = null, mixed $value = null): mixed
    {
        $registry = Registry::getInstance();

        if ($key === null) {
            return $registry;
        }

        if (func_num_args() >= 2 && $value !== null) {
            $registry->set($key, $value);
            return $value;
        }

        return $registry->get($key);
    }
}

if (!function_exists('str')) {
    /**
     * Wraps text in a chainable string utility.
     *
     * @param string $value Text to wrap.
     * @return Str String wrapper.
     */
    function str(string $value): Str
    {
        return new Str($value);
    }
}

if (!function_exists('arr')) {
    /**
     * Wraps an array in a chainable array utility.
     *
     * @param array<int|string, mixed> $value Values to wrap.
     * @return Arr Array wrapper.
     */
    function arr(array $value): Arr
    {
        return Arr::from($value);
    }
}

if (!function_exists('level')) {
    /**
     * Flattens nested arrays into dot-separated paths.
     *
     * @param array<int|string, mixed> $array Array to flatten.
     * @param int $target Maximum depth to expand.
     * @param int $current Current recursion depth.
     * @param string $prefix Prefix for generated paths.
     * @return array<string, mixed> Flattened leaf values.
     */
    function level(array $array, int $target = PHP_INT_MAX, int $current = 1, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            if (is_array($value) && $current < $target) {
                $result = array_merge($result, level($value, $target, $current + 1, $path));
            } else {
                $result[$path] = $value;
            }
        }

        return $result;
    }
}

if (!function_exists('token')) {
    /**
     * Parses token text into a query object.
     *
     * @param string $value Token text in `key:value|key:value` format.
     * @return TokenQuery Parsed token query.
     */
    function token(string $value): TokenQuery
    {
        return new TokenQuery($value);
    }
}

if (!function_exists('tokenize')) {
    /**
     * Serializes an associative array into token syntax.
     *
     * @param array<string, scalar|array<int, string>|null> $parts Token parts.
     * @return string `key:value|key:value` representation.
     */
    function tokenize(array $parts): string
    {
        return arr($parts)->tokenize();
    }
}

if (!function_exists('when')) {
    /**
     * Creates a Strukt date value.
     *
     * Numeric values are interpreted as Unix timestamps. The default `now`
     * value follows the simulated `Today` state when one is configured.
     *
     * @param string|int $date Date string or Unix timestamp.
     * @return StruktDateTime Date wrapper.
     * @throws \Exception When the date cannot be parsed.
     */
    function when(string|int $date = 'now'): StruktDateTime
    {
        if ($date === 'now') {
            return new StruktDateTime();
        }

        if (is_int($date) || (is_string($date) && is_numeric($date))) {
            if (StruktDateTime::isTimestamp($date)) {
                return StruktDateTime::fromTimestamp((int) $date);
            }
        }

        return new StruktDateTime((string) $date);
    }
}

if (!function_exists('period')) {
    /**
     * Creates a fluent controller for the simulated date period.
     *
     * @param \DateTimeInterface|null $start Optional inclusive period start.
     * @param \DateTimeInterface|null $end Optional inclusive period end.
     * @return object Period controller exposing `create()` and `reset()`.
     */
    function period(?\DateTimeInterface $start = null, ?\DateTimeInterface $end = null): object
    {
        return new class($start, $end) {
            /**
             * Creates a period controller and optionally configures a period.
             *
             * @param \DateTimeInterface|null $start Optional period start.
             * @param \DateTimeInterface|null $end Optional period end.
             */
            public function __construct(?\DateTimeInterface $start, ?\DateTimeInterface $end)
            {
                if ($start !== null) {
                    $this->create($start, $end);
                }
            }

            /**
             * Sets an inclusive simulated date period.
             *
             * @param \DateTimeInterface $start Inclusive lower boundary.
             * @param \DateTimeInterface|null $end Inclusive upper boundary.
             * @return static This period controller.
             * @throws \InvalidArgumentException When the end precedes the start.
             */
            public function create(\DateTimeInterface $start, ?\DateTimeInterface $end = null): static
            {
                $end ??= new \DateTime('9999-12-31 23:59:59.999999');
                Today::makePeriod($start, $end);

                return $this;
            }

            /**
             * Clears the period or changes the simulated date.
             *
             * @param \DateTimeInterface|null $date Simulated date, or `null` to clear.
             * @return static This period controller.
             */
            public function reset(?\DateTimeInterface $date = null): static
            {
                Today::reset($date);

                return $this;
            }
        };
    }
}

if (!function_exists('today')) {
    /**
     * Creates a value for the configured current day.
     *
     * @return Today Current simulated or system day.
     * @throws \OutOfBoundsException When the current day is outside a period.
     */
    function today(): Today
    {
        return new Today();
    }
}

if (!function_exists('format')) {
    /**
     * Registers or invokes a named formatter.
     *
     * Passing a closure registers it; any other value invokes the formatter
     * already registered under `$type`.
     *
     * @param string $type Formatter name.
     * @param mixed $value Closure to register or value to format.
     * @return mixed `null` after registration, or formatted value.
     * @throws \InvalidArgumentException When no formatter exists.
     */
    function format(string $type, mixed $value): mixed
    {
        $key = 'format.' . $type;

        if ($value instanceof \Closure) {
            reg($key, $value);
            return null;
        }

        $formatter = reg($key);
        if (!is_callable($formatter)) {
            throw new \InvalidArgumentException("Formatter [{$type}] is not registered.");
        }

        return $formatter($value);
    }

    /**
     * Formats a date as a relative human-readable description.
     *
     * @param \DateTimeInterface|string $value Date value to format.
     * @return string Relative date description.
     */
    format('humanize', static function (\DateTimeInterface|string $value): string {
        $date = $value instanceof \DateTimeInterface ? new StruktDateTime($value) : new StruktDateTime($value);

        return $date->when();
    });

    /**
     * Formats a date and time as `Y-m-d H:i:s`.
     *
     * @param \DateTimeInterface|string $value Date value to format.
     * @return string Date-time text.
     */
    format('datetime', static function (\DateTimeInterface|string $value): string {
        $date = $value instanceof \DateTimeInterface ? $value : new \DateTime($value);

        return $date->format('Y-m-d H:i:s');
    });

    /**
     * Formats a date as `Y-m-d`.
     *
     * @param \DateTimeInterface|string $value Date value to format.
     * @return string Date text.
     */
    format('date', static function (\DateTimeInterface|string $value): string {
        $date = $value instanceof \DateTimeInterface ? $value : new \DateTime($value);

        return $date->format('Y-m-d');
    });
}

if (!function_exists('stack')) {
    /**
     * Creates a handle for the process-local message stack.
     *
     * @param string|int|null $message Optional initial message.
     * @return Stack Stack handle.
     */
    function stack(string|int|null $message = null): Stack
    {
        return new Stack($message);
    }
}

if (!function_exists('json')) {
    /**
     * Creates a native JSON encoding helper.
     *
     * @param string|array<int|string, mixed> $value JSON text or value to encode.
     * @return object JSON helper exposing `pp()`, `decode()`, `encode()`, and `valid()`.
     */
    function json(string|array $value): object
    {
        return new class($value) {
            /**
             * Creates a JSON helper.
             *
             * @param string|array<int|string, mixed> $value JSON text or value.
             */
            public function __construct(private string|array $value)
            {
            }

            /**
             * Encodes the value as pretty-printed JSON.
             *
             * @return string Formatted JSON text.
             * @throws \JsonException When encoding fails.
             */
            public function pp(): string
            {
                return $this->encode(JSON_PRETTY_PRINT);
            }

            /**
             * Decodes JSON text into PHP values.
             *
             * @return mixed Decoded value, or the original array input.
             * @throws \JsonException When JSON text is invalid.
             */
            public function decode(): mixed
            {
                if (is_array($this->value)) {
                    return $this->value;
                }

                return json_decode($this->value, true, 512, JSON_THROW_ON_ERROR);
            }

            /**
             * Encodes the wrapped value as JSON.
             *
             * @param int $options Additional `json_encode()` flags.
             * @return string JSON text.
             * @throws \JsonException When encoding fails.
             */
            public function encode(int $options = 0): string
            {
                return json_encode($this->value, JSON_THROW_ON_ERROR | $options);
            }

            /**
             * Checks whether the wrapped string is valid JSON.
             *
             * @return bool True for valid JSON or an already decoded array.
             */
            public function valid(): bool
            {
                if (is_array($this->value)) {
                    return true;
                }

                json_decode($this->value);
                return json_last_error() === JSON_ERROR_NONE;
            }
        };
    }
}

if (!function_exists('alias')) {
    /**
     * Registers, reads, or lists class aliases.
     *
     * @param string|null $name Alias name, wildcard, or `null` for all aliases.
     * @param string|null $class Fully qualified class or interface to register.
     * @return array<string, string>|string|null Alias map, class name, or null.
     */
    function alias(?string $name = null, ?string $class = null): array|string|null
    {
        static $aliases = [];

        if ($name === null) {
            return $aliases;
        }

        if ($class !== null) {
            $aliases[$name] = $class;
            return $class;
        }

        if (str_ends_with($name, '*')) {
            $prefix = rtrim($name, '*');
            return array_filter($aliases, static fn (string $key): bool => str_starts_with($key, $prefix), ARRAY_FILTER_USE_KEY);
        }

        return $aliases[$name] ?? null;
    }
}

if (!function_exists('app')) {
    /**
     * Registers or resolves implementations for an aliased class/interface.
     *
     * @param string $name Alias optionally followed by `.*` or a class selector.
     * @param array<int, string>|null $classes Implementations to validate and register.
     * @return mixed Registered class name, class list, or null after registration.
     * @throws \InvalidArgumentException When the alias or implementation is invalid.
     */
    function app(string $name, ?array $classes = null): mixed
    {
        [$baseName, $selector] = array_pad(explode('.', $name, 2), 2, null);
        $base = alias($baseName) ?? (interface_exists($baseName) || class_exists($baseName) ? $baseName : null);

        if ($base === null) {
            throw new \InvalidArgumentException("Alias [{$baseName}] was not found.");
        }

        $key = 'app.' . $baseName;
        if ($classes !== null) {
            $valid = [];
            foreach ($classes as $class) {
                if (!is_string($class) || !class_exists($class)) {
                    throw new \InvalidArgumentException("Class [{$class}] was not found.");
                }

                $compatible = interface_exists($base)
                    ? is_a($class, $base, true)
                    : is_a($class, $base, true);
                if (!$compatible) {
                    throw new \InvalidArgumentException("Class [{$class}] does not implement [{$base}].");
                }

                $valid[] = $class;
            }

            reg($key, $valid);
            return null;
        }

        $registered = reg($key) ?? [];
        if ($selector === '*' || $selector === null) {
            return $selector === '*' ? $registered : ($registered[0] ?? null);
        }

        foreach ($registered as $class) {
            if ($class === $selector || str_ends_with($class, '\\' . $selector)) {
                return $class;
            }
        }

        return null;
    }
}

if (!function_exists('config')) {
    /**
     * Reads or writes a dot-separated configuration value.
     *
     * INI files in the package `cfg` directory are loaded on first access.
     *
     * @param string $key Configuration path.
     * @param array|string|int|bool|null $value Optional value to store.
     * @return mixed Configured value, or `null` when absent.
     */
    function config(string $key, array|string|int|bool|null $value = null): mixed
    {
        $registry = Registry::getInstance();
        if (!$registry->exists('config')) {
            $loaded = [];
            $directory = dirname(__DIR__) . '/cfg';
            if (is_dir($directory)) {
                foreach (glob($directory . '/*.ini') ?: [] as $file) {
                    $parsed = parse_ini_file($file, true, INI_SCANNER_TYPED);
                    if ($parsed !== false) {
                        $loaded[pathinfo($file, PATHINFO_FILENAME)] = $parsed;
                    }
                }
            }
            $registry->set('config', $loaded);
        }

        if (func_num_args() >= 2 && $value !== null) {
            $registry->set('config.' . $key, $value);
        }

        return $registry->get('config.' . $key);
    }
}

if (!function_exists('provider')) {
    /**
     * Instantiates a provider and invokes its `register()` method.
     *
     * @param string $class Fully qualified provider class name.
     * @return void
     * @throws \InvalidArgumentException When the class or method is unavailable.
     */
    function provider(string $class): void
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException("Provider [{$class}] was not found.");
        }

        $instance = new $class();
        if (!method_exists($instance, 'register')) {
            throw new \InvalidArgumentException("Provider [{$class}] has no register() method.");
        }

        $instance->register();
    }
}

if (!function_exists('singular')) {
    /**
     * Converts an English word to singular form.
     *
     * Symfony's inflector is used when installed; a small fallback handles
     * common suffixes for the standalone package.
     *
     * @param string $word Word to singularize.
     * @return string Singular form.
     */
    function singular(string $word): string
    {
        if (class_exists(\Symfony\Component\String\Inflector\EnglishInflector::class)) {
            $results = (new \Symfony\Component\String\Inflector\EnglishInflector())->singularize($word);
            return (string) ($results[0] ?? $word);
        }

        if (preg_match('/ies$/i', $word)) {
            return substr($word, 0, -3) . 'y';
        }
        if (preg_match('/(ches|shes|sses|xes|zes)$/i', $word)) {
            return substr($word, 0, -2);
        }
        if (preg_match('/(?<!ss)s$/i', $word)) {
            return substr($word, 0, -1);
        }

        return $word;
    }
}

if (!function_exists('plural')) {
    /**
     * Converts an English word to plural form.
     *
     * Symfony's inflector is used when installed; a small fallback handles
     * common suffixes for the standalone package.
     *
     * @param string $word Word to pluralize.
     * @return string Plural form.
     */
    function plural(string $word): string
    {
        if (class_exists(\Symfony\Component\String\Inflector\EnglishInflector::class)) {
            $results = (new \Symfony\Component\String\Inflector\EnglishInflector())->pluralize($word);
            return (string) ($results[0] ?? $word);
        }

        if (preg_match('/[^aeiou]y$/i', $word)) {
            return substr($word, 0, -1) . 'ies';
        }
        if (preg_match('/(s|x|z|ch|sh)$/i', $word)) {
            return $word . 'es';
        }

        return $word . 's';
    }
}

if (!function_exists('uuid')) {
    /**
     * Generates a UUID using Ramsey when available or a native version 4 fallback.
     *
     * @param int $version UUID version to generate.
     * @param array<int|string, mixed> $options Arguments passed to Ramsey UUID methods.
     * @return object UUID wrapper exposing `yield()` and string conversion.
     * @throws \InvalidArgumentException When the requested version is unavailable.
     * @throws \RuntimeException When a non-v4 UUID is requested without Ramsey.
     */
    function uuid(int $version = 4, array $options = []): object
    {
        if (class_exists(\Ramsey\Uuid\Uuid::class)) {
            $method = 'uuid' . $version;
            if (!method_exists(\Ramsey\Uuid\Uuid::class, $method)) {
                throw new \InvalidArgumentException("UUID version [{$version}] is not supported.");
            }

            $value = \Ramsey\Uuid\Uuid::$method(...$options)->toString();
        } else {
            if ($version !== 4) {
                throw new \RuntimeException('Only UUID version 4 is available without ramsey/uuid.');
            }

            $bytes = random_bytes(16);
            $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
            $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
            $value = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
        }

        return new class($value) {
            /**
             * Creates a UUID value wrapper.
             *
             * @param string $value UUID text.
             */
            public function __construct(private string $value)
            {
            }

            /**
             * Returns the UUID text.
             *
             * @return string UUID string.
             */
            public function yield(): string
            {
                return $this->value;
            }

            /**
             * Converts the UUID wrapper to text.
             *
             * @return string UUID string.
             */
            public function __toString(): string
            {
                return $this->value;
            }
        };
    }
}
