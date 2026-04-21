# Changelog

All notable changes to this library are documented in this file.

## [Unreleased]

## [v0.15.1] — 2026-04-21

- Fix `Events::on()` return type to restore compatibility with `EventEmitterTrait` (#73)

## [v0.15.0] — 2026-03-24

- **Breaking** Raise minimum PHP version to 8.2 (#63)
- Add strict type declarations (#63)
- Support PHP 8.5 (#61)

## [v0.14.0] — 2024-07-15

- **Breaking** Restrict `Str::symmetricSplit()`'s `$default` parameter to `?string` (#55)
- Support PHP 8.3

## [v0.13.0] — 2023-09-21

- **Breaking** `Seq` no longer treats function names as needles (#42)
- Add `iterable_value_first()` (#49)
- Add `yield_groups()` to group sorted iterators by callback-derived criteria (#46)
- Fix `Str::symmetricSplit()` to return padded array when subject is `null` (#45)
- Support PHP 8.2 (#39)


## [v0.12.1] — 2022-12-13

- Refine `Str` method signatures and `null` handling to improve PHP 8.1 compatibility (#37)

## [v0.12.0] — 2022-06-15

- **Breaking** Raise the minimum supported PHP version to 7.2 (#29)
- **Breaking** `Filter::equal()` and `Filter::unequal()` no longer perform wildcard matching;
  use `Filter::like()` and `Filter::unlike()` instead (#33)
- **Breaking** Remove mutator and closure support from `Properties` (#31)
- Add iteration support to `Properties` via `IteratorAggregate` (#32)
- Support PHP 8.1 (#29)

## [v0.11.0] — 2022-03-23

- **Breaking** Drop PHP 5.6 support; require PHP ≥ 7.0 (#28)
- Introduce `BaseFilter` trait for simple filter-aware classes (#28)

## [v0.10.0] — 2021-11-10

- Add the `Seq` utility for searching iterables by key or value (#26)
- Fix `Properties` to not mutate values on set (#23); resolve closures before
  mutation (#24)
- Support PHP 8 (#21)

## [v0.9.0] — 2021-03-19

- **Breaking** `Filter\Chain` and `Filter\Condition` no longer tolerate dynamic properties;
  use dedicated metadata instead (#20)
- Introduce `Filterable` contract interface and `Filters` trait for
  filter-aware objects (#19)
- Introduce `Data` class for arbitrary key-value metadata storage (#20)
- Introduce `MetaDataProvider` interface for filter rule metadata (#20)
- Extend `Filter\Chain` with `insertBefore()` and `insertAfter()` (#20)

## [v0.8.0] — 2021-01-14

- Introduce filter system: `Filter` facade and `Filter\Rule`, `Filter\Chain`
  (`All`, `Any`, `None`), and `Filter\Condition` (`Equal`, `Unequal`,
  `GreaterThan`, `GreaterThanOrEqual`, `LessThan`, `LessThanOrEqual`) (#15)
- Add `random_bytes()` polyfill for PHP < 7.0 (#18)

## [v0.7.0] — 2020-10-19

- Introduce `Properties` trait for dynamic typed property storage (#16)

## [v0.6.0] — 2020-10-12

- Introduce `Translator` contract interface (#14)

## [v0.5.0] — 2020-03-12

- **Breaking** Raise the minimum supported PHP version to 5.6 (#12)
- **Breaking** Refactor contracts and traits: rename `EventEmitter` → `Events`,
  `MessageContainer` → `Messages`; supersede `PaginationInterface` with
  `Paginatable`; supersede `ValidatorInterface` with `Validator` (#12)
- Introduce new `PluginLoader` interface and `Plugins` trait (#12)
- Introduce `PriorityQueue` class wrapping `SplPriorityQueue` with stable
  ordering (#13)

## [v0.4.0] — 2020-03-10

- Add `iterable_key_first()` for retrieving the first key from any iterable (#10)
- Add `Str::symmetricSplit()`, `Str::trimSplit()`, and `Str::startsWith()`

## [v0.3.0] — 2019-10-16

- Introduce `PaginationInterface` (#5)
- Introduce `Str` class with `camel()` case conversion
- Add `is_iterable()` polyfill for PHP < 7.1

## [v0.2.0] — 2019-05-16

- Introduce `EventEmitter` trait, wrapping
  [Evenement](https://github.com/igorw/evenement) (#8)

## [v0.1.0] — 2019-03-26

Initial release providing `AutoloadingPluginLoader`, `MessageContainer` trait,
`ValidatorInterface`, and type/iterable utility functions.

[Unreleased]: https://github.com/Icinga/ipl-stdlib/compare/v0.15.1...HEAD
[v0.15.1]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.15.1
[v0.15.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.15.0
[v0.14.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.14.0
[v0.13.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.13.0
[v0.12.1]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.12.1
[v0.12.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.12.0
[v0.11.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.11.0
[v0.10.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.10.0
[v0.9.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.9.0
[v0.8.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.8.0
[v0.7.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.7.0
[v0.6.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.6.0
[v0.5.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.5.0
[v0.4.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.4.0
[v0.3.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.3.0
[v0.2.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.2.0
[v0.1.0]: https://github.com/Icinga/ipl-stdlib/releases/tag/v0.1.0
