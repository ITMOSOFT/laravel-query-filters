<?php

declare(strict_types=1);

namespace ItmoSoft\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

final class QueryFilter
{
    /** @var array<string, string> */
    private array $filters = [];

    /** @var array<string, string> */
    private array $sorts = [];

    /** @var list<string> */
    private array $searchable = [];

    private ?string $defaultSort = null;

    /**
     * @param array<int|string, string> $filters Parameter => database column,
     *                                           or a list of identical parameter/column names.
     * @param array<int|string, string> $sorts Parameter => database column,
     *                                         or a list of identical parameter/column names.
     * @param list<string> $searchable Database columns that may be searched.
     */
    public static function make(
        array $filters = [],
        array $sorts = [],
        array $searchable = [],
    ): self {
        return (new self())
            ->allowedFilters($filters)
            ->allowedSorts($sorts)
            ->searchable($searchable);
    }

    /** @param array<int|string, string> $filters */
    public function allowedFilters(array $filters): self
    {
        $this->filters = $this->normalizeMap($filters);

        return $this;
    }

    /** @param array<int|string, string> $sorts */
    public function allowedSorts(array $sorts): self
    {
        $this->sorts = $this->normalizeMap($sorts);

        return $this;
    }

    /** @param list<string> $columns */
    public function searchable(array $columns): self
    {
        $this->searchable = array_values($columns);

        return $this;
    }

    public function defaultSort(?string $sort): self
    {
        $this->defaultSort = $sort;

        return $this;
    }

    /**
     * Apply allowlisted filters, search, and sorting to an Eloquent builder.
     *
     * Supported input:
     * - filter: ['status' => 'active', 'category_id' => [1, 2]]
     * - search: 'keyword'
     * - sort: '-created_at,name' (minus means descending)
     *
     * @param array<string, mixed> $input
     */
    public function apply(Builder $query, array $input): Builder
    {
        $this->applyFilters($query, $input['filter'] ?? []);
        $this->applySearch($query, $input['search'] ?? null);
        $this->applySorts($query, $input['sort'] ?? null);

        return $query;
    }

    /**
     * @param array<int|string, string> $map
     * @return array<string, string>
     */
    private function normalizeMap(array $map): array
    {
        $normalized = [];

        foreach ($map as $key => $value) {
            if (is_int($key)) {
                $normalized[$value] = $value;
            } else {
                $normalized[(string) $key] = $value;
            }
        }

        return $normalized;
    }

    private function applyFilters(Builder $query, mixed $input): void
    {
        if (! is_array($input)) {
            return;
        }

        foreach ($input as $parameter => $value) {
            $column = $this->filters[(string) $parameter] ?? null;

            if ($column === null || $value === '' || $value === []) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($column, $value);
            } elseif ($value === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $value);
            }
        }
    }

    private function applySearch(Builder $query, mixed $term): void
    {
        if (! is_string($term) || trim($term) === '' || $this->searchable === []) {
            return;
        }

        $term = trim($term);

        $query->where(function (Builder $nested) use ($term): void {
            foreach ($this->searchable as $index => $column) {
                $method = $index === 0 ? 'where' : 'orWhere';
                $nested->{$method}($column, 'LIKE', '%'.$term.'%');
            }
        });
    }

    private function applySorts(Builder $query, mixed $sort): void
    {
        $requested = is_string($sort) && trim($sort) !== ''
            ? explode(',', $sort)
            : [];

        if ($requested === [] && $this->defaultSort !== null) {
            $requested = [$this->defaultSort];
        }

        foreach ($requested as $requestedSort) {
            $requestedSort = trim($requestedSort);

            if ($requestedSort === '') {
                continue;
            }

            $descending = str_starts_with($requestedSort, '-');
            $parameter = ltrim($requestedSort, '-');
            $column = $this->sorts[$parameter] ?? null;

            if ($column === null) {
                continue;
            }

            $query->orderBy($column, $descending ? 'desc' : 'asc');
        }
    }
}
