# Icinga PHP Library - Standard Library

`ipl/stdlib` provides reusable building blocks for Icinga PHP libraries and
applications. It covers declarative filtering, event emission, string and
iterable utilities, lightweight data and message containers, and a
stable priority queue.

## Installation

The recommended way to install this library is via
[Composer](https://getcomposer.org):

```shell
composer require ipl/stdlib
```

`ipl/stdlib` requires PHP 8.2 or later with the `openssl` extension.

## Usage

### Filter Rows With Declarative Rules

Build composable filter trees with `ipl\Stdlib\Filter` and evaluate them
against arrays or objects:

```php
use ipl\Stdlib\Filter;

$filter = Filter::all(
    Filter::equal('problem', '1'),
    Filter::none(Filter::equal('handled', '1')),
    Filter::like('service', 'www.*')
);

$row = [
    'problem' => '1',
    'handled' => '0',
    'service' => 'www.icinga.com',
];

if (Filter::match($filter, $row)) {
    // The row matches the rule set.
}
```

Available condition factories: `equal`, `unequal`, `like`, `unlike`,
`greaterThan`, `greaterThanOrEqual`, `lessThan`, `lessThanOrEqual`.
Available logical factories: `all`, `any`, `none`, `not`.

Flatten nested chains when you need to inspect individual rules:

```php
foreach ($filter->yieldRules() as $rule) {
    // Inspect each leaf rule.
}
```

### Build Filters Incrementally

When an object needs to collect filter conditions over time, use the `Filters`
trait. It complements the `Filterable` contract and exposes `filter()`,
`orFilter()`, `notFilter()`, and `orNotFilter()`:

```php
use ipl\Stdlib\Contract\Filterable;
use ipl\Stdlib\Filter;
use ipl\Stdlib\Filters;

class Query implements Filterable
{
    use Filters;
}

$query = (new Query())
    ->filter(Filter::equal('problem', '1'))
    ->orNotFilter(Filter::equal('handled', '1'));

$filter = $query->getFilter();
```

## Events

The `Events` trait wraps [Evenement](https://github.com/igorw/evenement) and
adds event validation. Declare event name constants on the class to give
callers a typo-safe API and to let `isValidEvent()` enforce an explicit
allow-list:

```php
use ipl\Stdlib\Events;

class Connection
{
    use Events;

    public const ON_CONNECT = 'connected';
    public const ON_DISCONNECT = 'disconnected';

    protected function isValidEvent($event): bool
    {
        return in_array($event, [static::ON_CONNECT, static::ON_DISCONNECT], true);
    }

    public function open(): void
    {
        // ... connect ...
        $this->emit(self::ON_CONNECT, [$this]);
    }

    public function close(): void
    {
        // ... disconnect ...
        $this->emit(self::ON_DISCONNECT, [$this]);
    }
}

$conn = new Connection();
$conn->on(Connection::ON_CONNECT, function (Connection $c): void {
    echo "Connected\n";
});
$conn->on(Connection::ON_DISCONNECT, function (Connection $c): void {
    echo "Disconnected\n";
});

$conn->open();
$conn->close();
```

## Utility Helpers

### Str

`Str` offers string utilities that complement PHP's built-in functions.
It converts between naming conventions, splits and trims in one step, and
provides `startsWith` with case-insensitive matching.

```php
use ipl\Stdlib\Str;

// Convert snake_case or kebab-case identifiers to camelCase:
Str::camel('host_name');    // 'hostName'
Str::camel('display-name'); // 'displayName'

// Split on a delimiter and trim whitespace from every part in one pass:
Str::trimSplit(' foo , bar , baz '); // ['foo', 'bar', 'baz']
Str::trimSplit('root:secret', ':');  // ['root', 'secret']

// Always return exactly $limit parts: pads with null if the delimiter is
// absent, and fold any remainder into the last part if there are more
// separators than expected:
[$user, $pass] = Str::symmetricSplit('root', ':', 2);              // ['root', null]
[$user, $pass] = Str::symmetricSplit('root:secret:extra', ':', 2); // ['root', 'secret:extra']

// Case-insensitive prefix check:
Str::startsWith('Foobar', 'foo', caseSensitive: false); // true
Str::startsWith('foobar', 'foo');                       // true

// Empty-string check:
Str::isEmpty(null);  // true
Str::isEmpty('   '); // true
Str::isEmpty('0');   // false
```

### Seq

`Seq` searches arrays, iterators, and generators by value, key, or callback
without first materializing them into arrays. When the second argument to
`find` or `contains` is a non-callable, it is compared by value; pass a
closure to match by predicate instead:

```php
use ipl\Stdlib\Seq;

$users = [
    'alice' => 'admin',
    'bob'   => 'viewer',
];

Seq::contains($users, 'viewer'); // true

[$key, $value] = Seq::find($users, 'admin'); // ['alice', 'admin']

// Match by predicate. Returns as soon as a result is found:
[$key, $value] = Seq::find(
    $users,
    fn(string $role): bool => $role !== 'admin'
); // ['bob', 'viewer']

// Transform values while preserving keys:
$roles = Seq::map($users, fn(string $role): string => strtoupper($role));

// Yield unique values while preserving their first keys:
$roles = Seq::unique(['first' => 'admin', 'second' => 'admin', 'third' => 1]);
```

### Iterable Helpers

```php
use function ipl\Stdlib\iterable_key_first;
use function ipl\Stdlib\iterable_value_first;

$map = [
    'id'   => 42,
    'name' => 'Alice',
];

iterable_key_first($map);   // 'id'
iterable_value_first($map); // 42

// Works with generators and iterators. Does not require an array:
iterable_key_first(new ArrayIterator(['a' => 1])); // 'a'
iterable_key_first([]);                            // null
```

### Grouping With `yield_groups`

`yield_groups` partitions a pre-sorted traversable into named groups.
The callback must return at least the grouping criterion, but it can
also return a custom value and key. The traversable **must** be sorted
by the grouping criterion before being passed in; results are undefined
otherwise:

```php
use function ipl\Stdlib\yield_groups;

foreach (yield_groups($rows, fn(object $row): string => $row->category) as $category => $items) {
    // $items contains all rows for $category.
}
```

### Other Utility Classes

- `Data`: mutable key/value store
- `Messages`: collects user-facing messages and supports `sprintf`-style
  placeholders
- `PriorityQueue`: extends `SplPriorityQueue` with stable insertion-order for
  items at equal priority; iterate non-destructively with `yieldAll()`

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of notable changes.

## License

`ipl/stdlib` is licensed under the terms of the [MIT License](LICENSE.md).
