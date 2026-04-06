<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\DataTables;

use Centrex\TallUi\DataTable\{Action, Column};
use Centrex\TallUi\Livewire\DataTable;
use Centrex\TallUi\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UsersTable extends DataTable
{
    public function query(): Builder
    {
        return User::query();
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),
            Column::make('Status', 'status')->badge('neutral', [
                'active'   => 'success',
                'inactive' => 'warning',
            ]),
            Column::make('Actions')->actions([
                Action::make('Edit')
                    ->icon('o-pencil')
                    ->color('info')
                    ->route('users.edit', 'id'),
                Action::make('Delete')
                    ->icon('o-trash')
                    ->color('error')
                    ->confirm('Are you sure?')
                    ->emit('deleteUser', 'id'),
            ]),
        ];
    }
}
