<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

/**
 * Radial Bar (gauge) chart.
 *
 * Series is a flat array of percentages (0-100):
 *   [75, 55, 90]   — one arc per value
 *
 * Categories/labels map 1-to-1 with series values:
 *   ['CPU', 'Memory', 'Disk']
 */
class RadialBarChart extends BaseChart
{
    public string $startAngle = '-135';
    public string $endAngle   = '135';
    public bool $hollow       = true;   // donut-style hollow centre
    public string $track      = '';     // custom track background colour (CSS colour or '')

    protected function chartType(): string
    {
        return 'radialBar';
    }

    /** @return array<string, mixed> */
    protected function defaultOptions(): array
    {
        $hollow = $this->hollow
            ? ['size' => '60%', 'background' => 'transparent']
            : [];

        $track = $this->track !== ''
            ? ['background' => $this->track, 'strokeWidth' => '97%', 'margin' => 5]
            : ['strokeWidth' => '97%', 'margin' => 5];

        return [
            'plotOptions' => [
                'radialBar' => [
                    'startAngle' => (int) $this->startAngle,
                    'endAngle'   => (int) $this->endAngle,
                    'hollow'     => $hollow,
                    'track'      => $track,
                    'dataLabels' => [
                        'name'  => ['fontSize' => '12px', 'offsetY' => -10],
                        'value' => ['fontSize' => '20px', 'fontWeight' => 700, 'formatter' => "function(val){ return val + '%'; }"],
                        'total' => [
                            'show'      => true,
                            'label'     => 'Total',
                            'formatter' => "function(w){ const sum = w.globals.seriesTotals.reduce((a,b)=>a+b,0); return (sum/w.globals.series.length).toFixed(1)+'%'; }",
                        ],
                    ],
                ],
            ],
            'legend' => ['show' => true, 'position' => 'bottom'],
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
