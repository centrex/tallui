<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\DataTables;

use Centrex\TallUi\DataTable\Column;
use Centrex\TallUi\Livewire\DataTable;
use Centrex\TallUi\Tests\Fixtures\Models\Order;
use Illuminate\Database\Eloquent\Builder;

/**
 * Mirrors the real-world shape that motivated Column::exportKey(): a column
 * whose UI cell is a ->view() over a related model (here 'user.status',
 * standing in for e.g. a credit memo's status badge), where the value CSV
 * export actually needs ('user.email', standing in for a refund amount) is
 * a different attribute the plain $key never reaches.
 */
class ExportKeyOrdersTable extends DataTable
{
    public function query(): Builder
    {
        return Order::query()->with('user');
    }

    public function columns(): array
    {
        return [
            Column::make('Reference', 'reference'),
            Column::make('Customer', 'user.status')
                ->relation('user')
                ->exportKey('user.email'),
        ];
    }
}
