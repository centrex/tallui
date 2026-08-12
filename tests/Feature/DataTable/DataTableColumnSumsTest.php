<?php

declare(strict_types = 1);

use Centrex\TallUi\DataTable\Column;
use Centrex\TallUi\Tests\Fixtures\DataTables\{SummableOrdersTable, SummableUsersTable, SummableWithComputedColumnTable};
use Centrex\TallUi\Tests\Fixtures\Models\{Order, User};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

it('computes summable column totals correctly via a single aggregate query', function (): void {
    User::create(['name' => 'A', 'email' => 'a@test.com', 'is_active' => true]);
    User::create(['name' => 'B', 'email' => 'b@test.com', 'is_active' => true]);
    User::create(['name' => 'C', 'email' => 'c@test.com', 'is_active' => false]);

    $expectedIdSum = User::sum('id');
    $expectedActiveSum = User::sum('is_active');

    $instance = new SummableUsersTable;
    $instance->columnDefs = array_map(fn (Column $col): array => $col->toArray(), $instance->columns());

    DB::flushQueryLog();
    DB::enableQueryLog();

    $sums = $instance->getColumnSums();

    $sumQueries = array_filter(
        DB::getQueryLog(),
        fn (array $q): bool => stripos((string) $q['query'], 'sum(') !== false,
    );

    expect($sums['id'])->toBe((float) $expectedIdSum)
        ->and($sums['is_active'])->toBe((float) $expectedActiveSum)
        // One query aggregating every summable column, not one SUM() query per column.
        ->and($sumQueries)->toHaveCount(1);
});

it('computes sums correctly when the query also eager-loads a relation', function (): void {
    // Regression test: query()->with('user') must not make getColumnSums()
    // try to hydrate the 'user' relation onto the aggregate-only result row
    // (which never selects user_id) — that used to throw a MissingAttributeException
    // under Model::preventAccessingMissingAttributes().
    Model::preventAccessingMissingAttributes(true);
    Model::shouldBeStrict(true);

    $alice = User::create(['name' => 'Alice', 'email' => 'alice@test.com']);
    $bob = User::create(['name' => 'Bob', 'email' => 'bob@test.com']);
    Order::create(['user_id' => $alice->id, 'reference' => 'ORD-1', 'amount' => 100]);
    Order::create(['user_id' => $bob->id, 'reference' => 'ORD-2', 'amount' => 50.5]);

    $instance = new SummableOrdersTable;
    $instance->columnDefs = array_map(fn (Column $col): array => $col->toArray(), $instance->columns());

    $sums = $instance->getColumnSums();

    Model::preventAccessingMissingAttributes(false);
    Model::shouldBeStrict(false);

    expect($sums['amount'])->toBe(150.5);
});

it('computes sums correctly when the query itself adds a selectRaw() computed column', function (): void {
    // Regression test: query()->selectRaw('*, computed_col') must not leak into the
    // sums query — mixing that with SUM(...) and no GROUP BY is rejected by MySQL
    // under ONLY_FULL_GROUP_BY (error 1140) in production, even though sqlite
    // (used here) tolerates it silently. Assert on the captured SQL directly so
    // the regression is caught regardless of which DB driver enforces the rule.
    $alice = User::create(['name' => 'Alice', 'email' => 'alice@test.com']);
    Order::create(['user_id' => $alice->id, 'reference' => 'ORD-1', 'amount' => 100]);
    Order::create(['user_id' => $alice->id, 'reference' => 'ORD-2', 'amount' => 50.5]);

    $instance = new SummableWithComputedColumnTable;
    $instance->columnDefs = array_map(fn (Column $col): array => $col->toArray(), $instance->columns());

    DB::flushQueryLog();
    DB::enableQueryLog();

    $sums = $instance->getColumnSums();

    $sumQuery = collect(DB::getQueryLog())
        ->first(fn (array $q): bool => stripos((string) $q['query'], 'sum(') !== false);

    expect($sums['amount'])->toBe(150.5)
        ->and($sumQuery)->not->toBeNull()
        ->and((string) $sumQuery['query'])->not->toContain('doubled_amount');
});
