<?php

declare(strict_types = 1);

use Centrex\TallUi\DataTable\Column;
use Centrex\TallUi\Tests\Fixtures\DataTables\ExportKeyOrdersTable;
use Centrex\TallUi\Tests\Fixtures\Models\{Order, User};

function streamedCsv(ExportKeyOrdersTable $table): string
{
    $response = $table->exportCsv();

    ob_start();
    $response->sendContent();

    return (string) ob_get_clean();
}

it('exports the exportKey value instead of the display key when both are set', function (): void {
    $user = User::create(['name' => 'Alice', 'email' => 'alice@test.com', 'status' => 'active']);
    Order::create(['user_id' => $user->id, 'reference' => 'ORD-1', 'amount' => 100]);

    $instance = new ExportKeyOrdersTable;
    $instance->columnDefs = array_map(fn (Column $col): array => $col->toArray(), $instance->columns());

    $csv = streamedCsv($instance);

    expect($csv)->toContain('alice@test.com')
        ->and($csv)->not->toContain('active');
});

it('falls back to the display key when no exportKey is set', function (): void {
    $user = User::create(['name' => 'Bob', 'email' => 'bob@test.com', 'status' => 'inactive']);
    Order::create(['user_id' => $user->id, 'reference' => 'ORD-2', 'amount' => 50]);

    $table = new class extends ExportKeyOrdersTable
    {
        public function columns(): array
        {
            return [
                Column::make('Reference', 'reference'),
                Column::make('Customer', 'user.status')->relation('user'),
            ];
        }
    };
    $table->columnDefs = array_map(fn (Column $col): array => $col->toArray(), $table->columns());

    $csv = streamedCsv($table);

    expect($csv)->toContain('inactive')
        ->and($csv)->not->toContain('bob@test.com');
});
