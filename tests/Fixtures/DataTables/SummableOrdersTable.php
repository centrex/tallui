<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\DataTables;

use Centrex\TallUi\DataTable\Column;
use Centrex\TallUi\Livewire\DataTable;
use Centrex\TallUi\Tests\Fixtures\Models\Order;
use Illuminate\Database\Eloquent\Builder;

/**
 * Mirrors a real-world shape that broke getColumnSums(): a query with an
 * eager-loaded ->with() relation (via ->relation() below) alongside a
 * ->summable() column. Regression coverage for the eager-loading bug where
 * the sums query tried to hydrate the 'user' relation onto an aggregate-only
 * result row that never selected the user_id foreign key.
 */
class SummableOrdersTable extends DataTable
{
    public function query(): Builder
    {
        return Order::query()->with('user');
    }

    public function columns(): array
    {
        return [
            Column::make('Reference', 'reference'),
            Column::make('Customer', 'user.name')->relation('user'),
            Column::make('Amount', 'amount')->summable(),
        ];
    }
}
