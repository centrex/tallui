<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

class PieChart extends BaseChart
{
    public bool $donut = false;

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
            'labels' => $data['categories'] ?? [],
            'title'  => $this->title !== '' && $this->title !== '0' ? ['text' => $this->title, 'align' => 'left'] : [],
        ]);
    }
}
