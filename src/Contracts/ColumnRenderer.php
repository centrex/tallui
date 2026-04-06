<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Contracts;

interface ColumnRenderer
{
    /**
     * Render a table cell as trusted HTML.
     *
     * @param  \Illuminate\Database\Eloquent\Model|array<string, mixed>  $row
     */
    public function render(mixed $row, mixed $value): string;
}
