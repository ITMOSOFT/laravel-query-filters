<?php

declare(strict_types=1);

namespace ItmoSoft\QueryFilters;

use Illuminate\Support\ServiceProvider;

final class QueryFiltersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QueryFilter::class, static fn (): QueryFilter => new QueryFilter());
    }
}
