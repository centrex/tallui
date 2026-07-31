<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

class PieChart extends BaseChart
{
    public bool $donut = false;

    /**
     * Pie/donut charts conventionally call their slice labels "labels" (matching
     * ApexCharts' own `labels` chart option), not "categories" — every current
     * <livewire:tallui-pie-chart> call site in the monorepo (and the documented usage in
     * the root CLAUDE.md) passes :labels. $categories (inherited from BaseChart) is kept
     * as a fallback for the one caller that already uses it.
     */
    public array $labels = [];

    protected function chartType(): string
    {
        return $this->donut ? 'donut' : 'pie';
    }

    /** @return array<string, mixed> */
    protected function defaultOptions(): array
    {
        return [
            'legend'     => ['position' => 'bottom'],
            'dataLabels' => ['enabled' => true],
        ];
    }

    /**
     * Pie/Donut charts have a different series structure (flat array of numbers).
     * Override buildOptions to handle this.
     *
     * @return array<string, mixed>
     */
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
            'labels' => $this->labels !== [] ? $this->labels : ($data['categories'] ?? []),
            'title'  => $this->title !== '' && $this->title !== '0' ? ['text' => $this->title, 'align' => 'left'] : [],
        ]);
    }
}
