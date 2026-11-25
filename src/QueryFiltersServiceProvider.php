<?php

declare(strict_types=1);

namespace ItmoSoft\QueryFilters;

use Illuminate\Support\ServiceProvider;

final class QueryFiltersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // QueryFilter is a mutable fluent builder; each resolution must be isolated.
        $this->app->bind(QueryFilter::class, static fn (): QueryFilter => new QueryFilter());
    }
}
