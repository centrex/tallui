<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

class LineChart extends BaseChart
{
    public bool $smooth = false;

    protected function chartType(): string
    {
        return 'line';
    }

    /** @return array<string, mixed> */
    protected function defaultOptions(): array
    {
        return [
            'stroke' => [
                'curve' => $this->smooth ? 'smooth' : 'straight',
                'width' => 2,
            ],
            'markers' => ['size' => 4],
        ];
    }
}
