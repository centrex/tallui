<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

/**
 * Polar Area chart — like a pie but each sector extends outward based on value.
 *
 * Series is a flat array of values (same as pie/donut):
 *   [42, 18, 35, 27, 14]
 *
 * Categories are the sector labels:
 *   ['North', 'South', 'East', 'West', 'Central']
 */
class PolarAreaChart extends BaseChart
{
    protected function chartType(): string
    {
        return 'polarArea';
    }

    /** @return array<string, mixed> */
    protected function defaultOptions(): array
    {
        return [
            'stroke'  => ['colors' => ['transparent']],
            'fill'    => ['opacity' => 0.8],
            'legend'  => ['position' => 'bottom'],
            'plotOptions' => [
                'polarArea' => [
                    'rings'  => ['strokeWidth' => 1],
                    'spokes' => ['strokeWidth' => 1],
                ],
            ],
            'yaxis' => ['show' => false],
        ];
    }

    /** @return array<string, mixed> */
    public function buildOptions(): array
    {
        $data = $this->chartData;

        return array_merge_recursive($this->defaultOptions(), [
            'chart' => [
                'type'    => $this->chartType(),
                'height'  => $this->height,
                'theme'   => ['mode' => $this->theme],
                'toolbar' => ['show' => false],
            ],
            'series' => $data['series'] ?? [],
            'labels' => $data['categories'] ?? [],
            'title'  => $this->title !== '' ? ['text' => $this->title, 'align' => 'left', 'style' => ['fontSize' => '14px']] : [],
            'subtitle' => $this->subtitle !== '' ? ['text' => $this->subtitle, 'align' => 'left'] : [],
        ]);
    }
}
