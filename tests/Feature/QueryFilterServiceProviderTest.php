<?php

declare(strict_types=1);

namespace ItmoSoft\QueryFilters\Tests\Feature;

use ItmoSoft\QueryFilters\QueryFilter;
use ItmoSoft\QueryFilters\QueryFiltersServiceProvider;
use ItmoSoft\QueryFilters\Tests\TestCase;

final class QueryFilterServiceProviderTest extends TestCase
{
    public function test_container_resolves_a_fresh_query_filter_instance_each_time(): void
    {
        (new QueryFiltersServiceProvider($this->app))->register();

        $first = $this->app->make(QueryFilter::class);
        $second = $this->app->make(QueryFilter::class);

        $this->assertNotSame($first, $second);
    }
}
