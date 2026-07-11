<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\DataTables;

use Centrex\TallUi\Concerns\WithFilters;
use Centrex\TallUi\DataTable\{Column, Filter};
use Centrex\TallUi\Livewire\DataTable;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A DataTable that never touches the database — getRows()/getColumnSums()
 * are overridden to return hand-built data. Used to exercise financial
 * formatting, alignment, and layout changes in the Blade view without
 * needing a working DB driver (see DataTableViewRenderTest).
 */
class ArrayBackedDataTable extends DataTable
{
    use WithFilters;

    /** @var array<int, array<string, mixed>> */
    public array $fakeRows = [];

    public int $fakeTotal = 0;

    /** @var array<string, float> */
    public array $fakeSums = [];

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Amount', 'amount')->currency('USD')->summable()->sortable(),
            Column::make('Balance', 'balance')->currency('USD')->summable(),
            Column::make('Status', 'status')->badge('neutral', ['active' => 'success']),
            Column::make('Actions')->actions([]),
        ];
    }

    public function filters(): array
    {
        return [Filter::numberRange('Amount', 'amount')];
    }

    public function query(): Builder
    {
        throw new \RuntimeException('query() should never be called — getRows()/getColumnSums() are overridden.');
    }

    public function getRows(): PaginatorContract
    {
        return new LengthAwarePaginator(
            items: $this->fakeRows,
            total: $this->fakeTotal ?: count($this->fakeRows),
            perPage: $this->perPage,
            currentPage: $this->getPage(),
        );
    }

    public function getColumnSums(): array
    {
        return $this->fakeSums;
    }
}
