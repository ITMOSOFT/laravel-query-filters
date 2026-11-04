# Release guide

## Repository

**Repository name:** `laravel-query-filters`

**GitHub description:**

> A lightweight, allowlist-based query filtering, searching, and sorting package for Laravel Eloquent, with Laravel 12 and 13 support.

## Local testing

```bash
composer validate --no-check-publish
composer test
```

## Test Laravel 12 / Testbench 10

```bash
composer require --dev "orchestra/testbench:^10.0" --no-update --no-interaction
composer update --prefer-dist --no-interaction
composer show laravel/framework
composer show orchestra/testbench
composer test
```

## Test Laravel 13 / Testbench 11

Restore the package's original `composer.json` before switching the Testbench constraint, or use a clean clone:

```bash
git checkout -- composer.json composer.lock
composer require --dev "orchestra/testbench:^11.0" --no-update --no-interaction
composer update --prefer-dist --no-interaction
composer show laravel/framework
composer show orchestra/testbench
composer test
```

The CI workflows perform the same matrix independently. There is intentionally no committed `composer.lock`; each job configures its Testbench major without resolving, then runs a full `composer update`.

## First commit and push

```bash
git init
git add .
git commit -m "feat: initial Laravel query filters package"
git branch -M main
git remote add origin git@github.com:ITMOSOFT/laravel-query-filters.git
git push -u origin main
```

If the repository already exists, omit `git init`, `git branch -M main`, and `git remote add origin ...` as appropriate.

## First release

Only tag after both Laravel matrix jobs pass on `main`.

```bash
git checkout main
git pull --ff-only origin main
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

The release workflow reruns the Laravel matrix and creates the GitHub Release after both jobs pass.

**GitHub Release title:** `v1.0.0 — Initial Stable Release`

**GitHub Release description:**

> First stable release of `itmosoft/laravel-query-filters`.
>
> - Allowlisted exact-match query filters.
> - Array-value `whereIn` filters.
> - Configurable public parameter-to-column mapping.
> - Multi-column allowlisted search.
> - Allowlisted ascending and descending sorting.
> - Multiple sort fields and configurable default sort.
> - Laravel 12 and 13 support.
> - PHPUnit and GitHub Actions compatibility matrix.

## Packagist publication

1. Open https://packagist.org/packages/submit
2. Submit `https://github.com/ITMOSOFT/laravel-query-filters`.
3. Configure the Packagist GitHub webhook using the webhook URL and token provided by Packagist.
4. Verify that Packagist detects `v1.0.0`.
5. In a separate Laravel 12 or 13 application, run:

```bash
composer require itmosoft/laravel-query-filters:^1.0
```

## Smoke test

Apply the filter to a real Eloquent builder and verify filtered, searched, and sorted records. This package does not register Artisan commands or publish migrations/configuration.
