<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\Charts;

use Centrex\TallUi\Contracts\ChartDataProvider;

class TestDataProvider implements ChartDataProvider
{
    public function getData(): array
    {
        return [
            'series'     => [['name' => 'Revenue', 'data' => [500, 600, 700]]],
            'categories' => ['Jan', 'Feb', 'Mar'],
        ];
    }
}
