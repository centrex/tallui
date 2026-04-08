<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

/**
 * Range Area chart — shaded band between a high and a low value per x-point.
 *
 * Series data uses [low, high] pairs:
 *   [
 *     [
 *       'name' => 'Temperature Range',
 *       'data' => [
 *         ['x' => 'Jan', 'y' => [2, 12]],
 *         ['x' => 'Feb', 'y' => [3, 15]],
 *         ['x' => 'Mar', 'y' => [8, 22]],
 *       ],
 *     ],
 *   ]
 *
 * Multiple series overlay naturally, useful for confidence intervals, min/max bands, etc.
 */
class RangeAreaChart extends BaseChart
{
    public bool $smooth = true;

    protected function chartType(): string
    {
        return 'rangeArea';
    }

    /** @return array<string, mixed> */
    protected function defaultOptions(): array
    {
        return [
            'stroke' => [
                'curve' => $this->smooth ? 'smooth' : 'straight',
                'width' => 2,
            ],
            'fill' => [
                'opacity' => 0.3,
            ],
            'dataLabels' => ['enabled' => false],
            'legend'     => ['show' => true, 'position' => 'top'],
            'markers'    => ['hover' => ['sizeOffset' => 4]],
            'tooltip' => [
                'shared' => true,
                'y'      => [
                    'formatter' => "function(val, opts) {
                        if (Array.isArray(val)) return val[0] + ' – ' + val[1];
                        return val;
                    }",
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function buildOptions(): array
    {
        $data = $this->chartData;

        // RangeArea uses x/y pairs inside data — no separate categories array.
        return array_merge_recursive($this->defaultOptions(), [
            'chart' => [
                'type'       => $this->chartType(),
                'height'     => $this->height,
                'theme'      => ['mode' => $this->theme],
                'toolbar'    => ['show' => true],
                'animations' => ['enabled' => true],
            ],
            'series' => $data['series'] ?? [],
            'xaxis'  => ['type' => 'category', 'categories' => $data['categories'] ?? []],
            'title'  => $this->title !== '' ? ['text' => $this->title, 'align' => 'left', 'style' => ['fontSize' => '14px']] : [],
            'subtitle' => $this->subtitle !== '' ? ['text' => $this->subtitle, 'align' => 'left'] : [],
        ]);
    }
}
