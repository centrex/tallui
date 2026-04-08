<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

/**
 * Treemap chart — hierarchical area visualisation.
 *
 * Series format (flat):
 *   [['name' => 'Group', 'data' => [['x' => 'Label', 'y' => 42], ...]]]
 *
 * Or multi-level (each series = one colour group):
 *   [
 *     ['name' => 'Category A', 'data' => [['x' => 'Item 1', 'y' => 10], ...]],
 *     ['name' => 'Category B', 'data' => [['x' => 'Item 2', 'y' => 25], ...]],
 *   ]
 */
class TreemapChart extends BaseChart
{
    public bool $distributed = true;   // each cell gets its own colour

    public bool $enableShades = true;

    protected function chartType(): string
    {
        return 'treemap';
    }

    /** @return array<string, mixed> */
    protected function defaultOptions(): array
    {
        return [
            'dataLabels' => [
                'enabled'   => true,
                'style'     => ['fontSize' => '12px'],
                'formatter' => 'function(text, op){ return [text, op.value]; }',
                'offsetY'   => -4,
            ],
            'plotOptions' => [
                'treemap' => [
                    'distributed'  => $this->distributed,
                    'enableShades' => $this->enableShades,
                ],
            ],
            'legend' => ['show' => false],
        ];
    }

    /** @return array<string, mixed> */
    public function buildOptions(): array
    {
        $data = $this->chartData;

        return array_merge_recursive($this->defaultOptions(), [
            'chart' => [
                'type'       => $this->chartType(),
                'height'     => $this->height,
                'theme'      => ['mode' => $this->theme],
                'toolbar'    => ['show' => true],
                'animations' => ['enabled' => true],
            ],
            'series'   => $data['series'] ?? [],
            'title'    => $this->title !== '' ? ['text' => $this->title, 'align' => 'left', 'style' => ['fontSize' => '14px']] : [],
            'subtitle' => $this->subtitle !== '' ? ['text' => $this->subtitle, 'align' => 'left'] : [],
        ]);
    }
}
