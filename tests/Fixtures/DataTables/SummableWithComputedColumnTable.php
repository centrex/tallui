<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\DataTables;

use Centrex\TallUi\DataTable\Column;
use Centrex\TallUi\Livewire\DataTable;
use Centrex\TallUi\Tests\Fixtures\Models\Order;
use Illuminate\Database\Eloquent\Builder;

/**
 * Mirrors a real-world shape that broke getColumnSums(): a query() that adds its
 * own ->selectRaw() computed column (e.g. for a sortable virtual column) alongside
 * a ->summable() column. selectRaw()/addSelect() on the underlying query builder
 * appends rather than replaces, so without clearing the columns first, the sums
 * query mixed the computed column with SUM(...) and no GROUP BY — rejected by
 * MySQL under ONLY_FULL_GROUP_BY (error 1140), though sqlite lets it through
 * silently, which is why this needs its own regression coverage.
 */
class SummableWithComputedColumnTable extends DataTable
{
    public function query(): Builder
    {
        return Order::query()->selectRaw('*, (amount * 2) as doubled_amount');
    }

    public function columns(): array
    {
        return [
            Column::make('Reference', 'reference'),
            Column::make('Amount', 'amount')->summable(),
        ];
    }
}
