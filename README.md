# ITMOSOFT Laravel Query Filters

A lightweight, allowlist-based query filtering, searching, and sorting package for Laravel Eloquent.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

```bash
composer require itmosoft/laravel-query-filters
```

The service provider is registered through Laravel package auto-discovery; no manual registration is required. The package does not publish configuration or migrations.

## Basic usage

```php
use ItmoSoft\QueryFilters\QueryFilter;

$filter = QueryFilter::make(
    filters: ['status', 'category_id'],
    sorts: ['title', 'created_at'],
    searchable: ['title', 'summary'],
)->defaultSort('-created_at');

$query = Article::query();

$filter->apply($query, [
    'filter' => [
        'status' => 'published',
        'category_id' => [2, 5],
    ],
    'search' => 'Laravel',
    'sort' => '-created_at,title',
]);

$articles = $query->paginate(15);
```

## Rename public filter/sort parameters

Use associative arrays to map request parameter names to database columns:

```php
$filter = QueryFilter::make(
    filters: [
        'state' => 'status',
        'category' => 'category_id',
    ],
    sorts: [
        'published' => 'published_at',
        'headline' => 'title',
    ],
    searchable: ['title', 'summary'],
);
```

Then pass:

```php
$filter->apply(Article::query(), [
    'filter' => ['state' => 'published'],
    'sort' => '-published,headline',
    'search' => 'Laravel',
]);
```

## Supported input

| Input | Behavior |
|---|---|
| `filter[field]=value` | Exact match for an allowlisted filter |
| `filter[field][]=a&filter[field][]=b` | `whereIn` for an allowlisted filter |
| `filter[field]=null` in a PHP input array | `whereNull` |
| `search=keyword` | `LIKE` search across configured searchable columns |
| `sort=name` | Ascending sort |
| `sort=-name` | Descending sort |
| `sort=-created_at,title` | Multiple allowlisted sorts |

Empty string and empty array filter values are ignored. Unknown filters and sorts are ignored. Search is ignored when no searchable columns are configured.

## Security model

Only explicitly allowlisted filter columns, sort columns, and search columns are used. Never build allowlists directly from untrusted request input. Map public parameter names to known database columns when they differ.

This package intentionally does not perform pagination, authorization, relationship includes, arbitrary operators, or automatic request-global access. Compose it with Laravel pagination and your application's policies.

## Testing

```bash
composer install
composer test
```

The GitHub Actions matrix tests PHP 8.3 against Laravel 12 / Testbench 10 and Laravel 13 / Testbench 11.

## License

MIT. See [LICENSE](LICENSE).
