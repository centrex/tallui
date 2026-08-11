<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\DataTables;

use Centrex\TallUi\DataTable\Column;
use Centrex\TallUi\Livewire\DataTable;
use Centrex\TallUi\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SummableUsersTable extends DataTable
{
    public function query(): Builder
    {
        return User::query();
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')->summable(),
            Column::make('Active', 'is_active')->summable(),
            Column::make('Name', 'name'),
        ];
    }
}
