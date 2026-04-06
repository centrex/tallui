<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Contracts;

interface ChartDataProvider
{
    /**
     * Return chart series and categories.
     *
     * Expected format:
     * [
     *   'series'     => [['name' => 'Revenue', 'data' => [100, 200, 150]]],
     *   'categories' => ['Jan', 'Feb', 'Mar'],
     * ]
     *
     * @return array{series: array<int, array<string, mixed>>, categories: array<int, mixed>}
     */
    public function getData(): array;
}
