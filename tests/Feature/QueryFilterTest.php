<?php

declare(strict_types=1);

namespace ItmoSoft\QueryFilters\Tests\Feature;

use ItmoSoft\QueryFilters\QueryFilter;
use ItmoSoft\QueryFilters\Tests\TestCase;

final class QueryFilterTest extends TestCase
{
    public function test_it_filters_by_an_allowlisted_field(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(filters: ['status']);

        $this->assertSame(2, $filter->apply($query, ['filter' => ['status' => 'published']])->count());
    }

    public function test_it_maps_public_filter_names_to_database_columns(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(filters: ['state' => 'status']);

        $this->assertSame(1, $filter->apply($query, ['filter' => ['state' => 'draft']])->count());
    }

    public function test_it_ignores_non_allowlisted_filters(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(filters: ['status']);

        $this->assertSame(3, $filter->apply($query, ['filter' => ['unknown' => 'x']])->count());
    }

    public function test_it_supports_where_in_for_array_values(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(filters: ['category_id']);

        $this->assertSame(3, $filter->apply($query, ['filter' => ['category_id' => [1, 2]]])->count());
    }

    public function test_it_supports_null_filter_values(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(filters: ['status']);

        $this->assertSame(0, $filter->apply($query, ['filter' => ['status' => null]])->count());
    }

    public function test_it_ignores_empty_filter_values(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(filters: ['status']);

        $this->assertSame(3, $filter->apply($query, ['filter' => ['status' => '']])->count());
    }

    public function test_it_searches_across_allowlisted_columns(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(searchable: ['title', 'status']);

        $this->assertSame(2, $filter->apply($query, ['search' => 'Laravel'])->count());
    }

    public function test_it_ignores_search_when_no_searchable_columns_are_configured(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make();

        $this->assertSame(3, $filter->apply($query, ['search' => 'Laravel'])->count());
    }

    public function test_it_sorts_ascending_by_an_allowlisted_column(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(sorts: ['title']);

        $titles = $filter->apply($query, ['sort' => 'title'])->pluck('title')->all();

        $this->assertSame(['Laravel filters', 'Laravel testing', 'PHP testing'], $titles);
    }

    public function test_it_sorts_descending_with_a_leading_minus(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(sorts: ['title']);

        $titles = $filter->apply($query, ['sort' => '-title'])->pluck('title')->all();

        $this->assertSame(['PHP testing', 'Laravel testing', 'Laravel filters'], $titles);
    }

    public function test_it_supports_multiple_sorts(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(sorts: ['status', 'title']);

        $rows = $filter->apply($query, ['sort' => 'status,title'])->get();

        $this->assertSame(['draft', 'published', 'published'], $rows->pluck('status')->all());
        $this->assertSame(['PHP testing', 'Laravel filters', 'Laravel testing'], $rows->pluck('title')->all());
    }

    public function test_it_ignores_non_allowlisted_sorts(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(sorts: ['title']);

        $this->assertSame(3, $filter->apply($query, ['sort' => 'password'])->count());
    }

    public function test_it_applies_default_sort_when_no_sort_is_requested(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make(sorts: ['title'])->defaultSort('-title');

        $titles = $filter->apply($query, [])->pluck('title')->all();

        $this->assertSame(['PHP testing', 'Laravel testing', 'Laravel filters'], $titles);
    }

    public function test_it_returns_the_same_builder_instance(): void
    {
        $query = $this->articleQuery();
        $filter = QueryFilter::make();

        $this->assertSame($query, $filter->apply($query, []));
    }
}
