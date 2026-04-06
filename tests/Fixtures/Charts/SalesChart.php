<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\Charts;

use Centrex\TallUi\Livewire\Charts\LineChart;

class SalesChart extends LineChart
{
    protected function data(): array
    {
        return [
            'series'     => [['name' => 'Sales', 'data' => [100, 200, 150, 300]]],
            'categories' => ['Q1', 'Q2', 'Q3', 'Q4'],
        ];
    }
}
