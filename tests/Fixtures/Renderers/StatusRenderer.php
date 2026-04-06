<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\Renderers;

use Centrex\TallUi\Contracts\ColumnRenderer;

class StatusRenderer implements ColumnRenderer
{
    public function render(mixed $row, mixed $value): string
    {
        $color = match ($value) {
            'active'   => 'green',
            'inactive' => 'orange',
            default    => 'gray',
        };

        return '<span style="color:' . $color . '">' . e((string) $value) . '</span>';
    }
}
