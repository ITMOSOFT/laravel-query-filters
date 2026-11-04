<?php

declare(strict_types=1);

namespace ItmoSoft\QueryFilters\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('status');
            $table->unsignedInteger('category_id');
            $table->timestamps();
        });

        DB::table('articles')->insert([
            ['title' => 'Laravel filters', 'status' => 'published', 'category_id' => 1, 'created_at' => '2026-01-01 00:00:00', 'updated_at' => '2026-01-01 00:00:00'],
            ['title' => 'PHP testing', 'status' => 'draft', 'category_id' => 2, 'created_at' => '2026-01-02 00:00:00', 'updated_at' => '2026-01-02 00:00:00'],
            ['title' => 'Laravel testing', 'status' => 'published', 'category_id' => 2, 'created_at' => '2026-01-03 00:00:00', 'updated_at' => '2026-01-03 00:00:00'],
        ]);
    }

    protected function articleQuery(): Builder
    {
        $model = new class extends Model {
            protected $table = 'articles';

            public $timestamps = false;
        };

        return $model->newQuery();
    }
}
