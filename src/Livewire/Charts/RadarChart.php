<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

/**
 * Radar (spider/web) chart.
 *
 * Standard series format:
 *   [['name' => 'Product A', 'data' => [80, 50, 30, 40, 100, 20]]]
 *
 * Categories are the axis labels (spokes):
 *   ['Speed', 'Power', 'Range', 'Efficiency', 'Comfort', 'Safety']
 */
class RadarChart extends BaseChart
{
    public bool $polygon = true;    // polygon grid vs circular grid
    public int $polygonCount = 6;   // number of concentric polygon rings

    protected function chartType(): string
    {
        return 'radar';
    }

    /** @return array<string, mixed> */
    protected function defaultOptions(): array
    {
        return [
            'stroke'     => ['width' => 2],
            'fill'       => ['opacity' => 0.2],
            'markers'    => ['size' => 4],
            'dataLabels' => ['enabled' => false],
            'yaxis'      => ['show' => false],
            'plotOptions' => [
                'radar' => [
                    'polygons' => [
                        'strokeColors'   => ['oklch(var(--bc)/0.15)'],
                        'connectorColors' => 'oklch(var(--bc)/0.15)',
                        'fill'           => ['colors' => ['transparent']],
                    ],
                ],
            ],
        ];
    }
}
