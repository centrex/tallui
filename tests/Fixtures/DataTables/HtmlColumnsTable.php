<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\DataTables;

use Centrex\TallUi\DataTable\Column;
use Centrex\TallUi\Livewire\DataTable;
use Centrex\TallUi\Tests\Fixtures\Models\User;
use Centrex\TallUi\Tests\Fixtures\Renderers\StatusRenderer;
use Illuminate\Database\Eloquent\Builder;

class HtmlColumnsTable extends DataTable
{
    public function query(): Builder
    {
        return User::query();
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name'),
            Column::make('Status', 'status')->html(StatusRenderer::class),
            Column::make('Raw', 'name')->raw(),
        ];
    }
}
