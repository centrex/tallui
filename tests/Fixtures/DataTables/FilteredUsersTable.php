<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\DataTables;

use Centrex\TallUi\Concerns\WithFilters;
use Centrex\TallUi\DataTable\{Column, Filter};
use Centrex\TallUi\Livewire\DataTable;
use Centrex\TallUi\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Builder;

class FilteredUsersTable extends DataTable
{
    use WithFilters;

    public function query(): Builder
    {
        return User::query();
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Status', 'status')->badge(),
            Column::make('Active', 'is_active'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::text('Name', 'name')->placeholder('Search name…'),
            Filter::select('Status', 'status', ['active' => 'Active', 'inactive' => 'Inactive']),
            Filter::boolean('Active', 'is_active'),
            Filter::dateRange('Joined', 'joined_at'),
        ];
    }
}
