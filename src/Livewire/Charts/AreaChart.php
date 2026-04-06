<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

class AreaChart extends BaseChart
{
    public bool $stacked = false;

    protected function chartType(): string
    {
        return 'area';
    }

    /** @return array<string, mixed> */
    protected function defaultOptions(): array
    {
        return [
            'stroke' => ['curve' => 'smooth', 'width' => 2],
            'fill'   => ['type' => 'gradient', 'gradient' => ['opacityFrom' => 0.4, 'opacityTo' => 0.05]],
            'chart'  => ['stacked' => $this->stacked],
            'dataLabels' => ['enabled' => false],
        ];
    }
}
