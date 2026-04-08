<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

/**
 * Mixed (combo) chart — combines bar, line, and area series in one chart.
 *
 * Series must include a `type` key per series:
 *   [
 *     ['name' => 'Revenue',  'type' => 'bar',  'data' => [...]],
 *     ['name' => 'Trend',    'type' => 'line', 'data' => [...]],
 *     ['name' => 'Forecast', 'type' => 'area', 'data' => [...]],
 *   ]
 */
class MixedChart extends BaseChart
{
    /** Primary chart type for the canvas; ApexCharts uses per-series `type` for the actual rendering. */
    protected function chartType(): string
    {
        return 'mixed';
    }

    /** @return array<string, mixed> */
    protected function defaultOptions(): array
    {
        return [
            'stroke'     => ['width' => [0, 3, 2], 'curve' => 'smooth'],
            'dataLabels' => ['enabled' => false],
            'legend'     => ['show' => true, 'position' => 'top'],
            'fill'       => [
                'type'    => ['solid', 'solid', 'gradient'],
                'opacity' => [1, 1, 0.4],
            ],
            'plotOptions' => [
                'bar' => ['borderRadius' => 4, 'columnWidth' => '55%'],
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function buildOptions(): array
    {
        $data = $this->chartData;

        // Mixed charts need chart.type = 'line' with per-series `type` keys.
        $base = array_merge_recursive($this->defaultOptions(), [
            'chart' => [
                'type'       => 'line',
                'height'     => $this->height,
                'theme'      => ['mode' => $this->theme],
                'toolbar'    => ['show' => true],
                'animations' => ['enabled' => true],
            ],
            'series' => $data['series'] ?? [],
            'xaxis'  => ['categories' => $data['categories'] ?? []],
            'title'  => $this->title !== '' ? ['text' => $this->title, 'align' => 'left', 'style' => ['fontSize' => '14px']] : [],
            'subtitle' => $this->subtitle !== '' ? ['text' => $this->subtitle, 'align' => 'left', 'style' => []] : [],
        ]);

        // Ensure each series carries its own type (default 'bar' if not set)
        if (isset($base['series']) && is_array($base['series'])) {
            $base['series'] = array_map(
                static fn (array $s): array => array_merge(['type' => 'bar'], $s),
                $base['series'],
            );
        }

        return $base;
    }
}
