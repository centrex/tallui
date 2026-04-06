<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire\Charts;

use Centrex\TallUi\Concerns\CachesData;
use Centrex\TallUi\Contracts\ChartDataProvider;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

abstract class BaseChart extends Component
{
    use CachesData;

    public string $title = '';

    public string $subtitle = '';

    public int $height = 0;

    public int $poll = 0;

    public string $theme = '';

    /** FQCN implementing ChartDataProvider */
    public ?string $dataProvider = null;

    abstract protected function chartType(): string;

    /** @return array<string, mixed> */
    abstract protected function defaultOptions(): array;

    /**
     * Override in a subclass to provide inline data.
     *
     * @return array{series: array<int, array<string, mixed>>, categories: array<int, mixed>}
     */
    protected function data(): array
    {
        return ['series' => [], 'categories' => []];
    }

    /**
     * Resolve data from either the dataProvider or the inline data() method.
     *
     * @return array{series: array<int, array<string, mixed>>, categories: array<int, mixed>}
     */
    protected function resolveData(): array
    {
        if ($this->dataProvider !== null && class_exists($this->dataProvider)) {
            /** @var ChartDataProvider $provider */
            $provider = app($this->dataProvider);

            return $provider->getData();
        }

        return $this->data();
    }

    /**
     * Chart data — cached when $cacheTtl > 0, memoized within the request by #[Computed].
     *
     * @return array{series: array<int, array<string, mixed>>, categories: array<int, mixed>}
     */
    #[Computed]
    public function chartData(): array
    {
        return $this->rememberCacheTracked(
            $this->chartCacheKey(),
            fn (): array => $this->resolveData(),
        );
    }

    /**
     * Cache key for this chart's data.
     * Chart data is not per-user/per-request-state so the key only encodes
     * the component class and data provider.
     */
    protected function chartCacheKey(): string
    {
        return $this->cacheKey(
            'chart',
            md5(static::class . ($this->dataProvider ?? '')),
        );
    }

    public function mount(): void
    {
        if ($this->height === 0) {
            $this->height = (int) config('tallui.charts.default_height', 350);
        }

        if ($this->poll === 0) {
            $this->poll = (int) config('tallui.charts.default_poll', 0);
        }

        if ($this->theme === '') {
            $this->theme = (string) config('tallui.charts.theme', 'light');
        }

        if ($this->cacheTtl === 0) {
            $this->cacheTtl = (int) config('tallui.charts.cache_ttl', 0);
        }
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
            'xaxis' => [
                'categories' => $data['categories'] ?? [],
            ],
            'series' => $data['series'] ?? [],
            'title'  => $this->title ? [
                'text'  => $this->title,
                'align' => 'left',
            ] : [],
            'subtitle' => $this->subtitle ? [
                'text'  => $this->subtitle,
                'align' => 'left',
            ] : [],
        ]);
    }

    public function render(): View
    {
        return view('tallui::livewire.charts.' . $this->chartType());
    }
}
