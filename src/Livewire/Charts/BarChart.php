<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

class BarChart extends BaseChart
{
    public bool $horizontal = false;

    protected function chartType(): string
    {
        return 'bar';
    }

    /** @return array<string, mixed> */
    protected function defaultOptions(): array
    {
        return [
            'plotOptions' => [
                'bar' => [
                    'horizontal'   => $this->horizontal,
                    'borderRadius' => 4,
                ],
            ],
            'dataLabels' => ['enabled' => false],
        ];
    }
}
