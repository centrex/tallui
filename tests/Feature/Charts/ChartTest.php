<?php

declare(strict_types = 1);

use Centrex\TallUi\Livewire\Charts\AreaChart;
use Centrex\TallUi\Livewire\Charts\BarChart;
use Centrex\TallUi\Livewire\Charts\LineChart;
use Centrex\TallUi\Livewire\Charts\PieChart;
use Centrex\TallUi\Tests\Fixtures\Charts\SalesChart;
use Centrex\TallUi\Tests\Fixtures\Charts\TestDataProvider;

use function Pest\Livewire\livewire;

describe('LineChart', function (): void {
    it('renders without error', function (): void {
        livewire(LineChart::class)->assertOk();
    });

    it('applies config height default', function (): void {
        config(['tallui.charts.default_height' => 400]);

        livewire(LineChart::class)
            ->assertSet('height', 400);
    });

    it('accepts height prop override', function (): void {
        livewire(LineChart::class, ['height' => 200])
            ->assertSet('height', 200);
    });

    it('accepts title prop', function (): void {
        livewire(LineChart::class, ['title' => 'Revenue'])
            ->assertSee('Revenue');
    });

    it('smooth prop toggles curve style', function (): void {
        $chart = livewire(LineChart::class, ['smooth' => true])
            ->instance();

        expect($chart->defaultOptions()['stroke']['curve'])->toBe('smooth');
    });

    it('non-smooth uses straight curve', function (): void {
        $chart = livewire(LineChart::class, ['smooth' => false])
            ->instance();

        expect($chart->defaultOptions()['stroke']['curve'])->toBe('straight');
    });

    it('includes ApexCharts cdn in output', function (): void {
        livewire(LineChart::class)
            ->assertSee(config('tallui.charts.apexcharts_cdn'));
    });

    it('outputs wire:poll when poll > 0', function (): void {
        livewire(LineChart::class, ['poll' => 5000])
            ->assertSee('wire:poll.5000ms');
    });

    it('does not output wire:poll when poll is 0', function (): void {
        livewire(LineChart::class, ['poll' => 0])
            ->assertDontSee('wire:poll');
    });
});

describe('BarChart', function (): void {
    it('renders without error', function (): void {
        livewire(BarChart::class)->assertOk();
    });

    it('horizontal prop is false by default', function (): void {
        $chart = livewire(BarChart::class)->instance();

        expect($chart->defaultOptions()['plotOptions']['bar']['horizontal'])->toBeFalse();
    });

    it('horizontal prop sets horizontal bar chart', function (): void {
        $chart = livewire(BarChart::class, ['horizontal' => true])->instance();

        expect($chart->defaultOptions()['plotOptions']['bar']['horizontal'])->toBeTrue();
    });
});

describe('PieChart', function (): void {
    it('renders as pie by default', function (): void {
        $chart = livewire(PieChart::class)->instance();

        expect($chart->chartType())->toBe('pie');
    });

    it('renders as donut when donut prop is true', function (): void {
        $chart = livewire(PieChart::class, ['donut' => true])->instance();

        expect($chart->chartType())->toBe('donut');
    });

    it('renders without error', function (): void {
        livewire(PieChart::class)->assertOk();
    });
});

describe('AreaChart', function (): void {
    it('renders without error', function (): void {
        livewire(AreaChart::class)->assertOk();
    });

    it('stacked is false by default', function (): void {
        $chart = livewire(AreaChart::class)->instance();

        expect($chart->defaultOptions()['chart']['stacked'])->toBeFalse();
    });

    it('stacked prop sets stacked mode', function (): void {
        $chart = livewire(AreaChart::class, ['stacked' => true])->instance();

        expect($chart->defaultOptions()['chart']['stacked'])->toBeTrue();
    });
});

describe('Chart data provider', function (): void {
    it('uses data() from subclass when no dataProvider set', function (): void {
        $chart = livewire(SalesChart::class)->instance();
        $data  = $chart->chartData;

        expect($data['categories'])->toBe(['Q1', 'Q2', 'Q3', 'Q4'])
            ->and($data['series'][0]['name'])->toBe('Sales');
    });

    it('resolves a ChartDataProvider class', function (): void {
        $chart = livewire(LineChart::class, [
            'dataProvider' => TestDataProvider::class,
        ])->instance();

        $data = $chart->chartData;

        expect($data['categories'])->toBe(['Jan', 'Feb', 'Mar'])
            ->and($data['series'][0]['name'])->toBe('Revenue');
    });

    it('returns empty series when no data and no provider', function (): void {
        $chart = livewire(LineChart::class)->instance();

        expect($chart->chartData)->toBe(['series' => [], 'categories' => []]);
    });
});

describe('buildOptions()', function (): void {
    it('includes chart type in options', function (): void {
        $chart   = livewire(LineChart::class)->instance();
        $options = $chart->buildOptions();

        expect($options['chart']['type'])->toBe('line');
    });

    it('includes height in options', function (): void {
        $chart   = livewire(LineChart::class, ['height' => 300])->instance();
        $options = $chart->buildOptions();

        expect($options['chart']['height'])->toBe(300);
    });

    it('includes title when set', function (): void {
        $chart   = livewire(LineChart::class, ['title' => 'My Chart'])->instance();
        $options = $chart->buildOptions();

        expect($options['title']['text'])->toBe('My Chart');
    });

    it('includes categories from data', function (): void {
        $chart   = livewire(SalesChart::class)->instance();
        $options = $chart->buildOptions();

        expect($options['xaxis']['categories'])->toBe(['Q1', 'Q2', 'Q3', 'Q4']);
    });
});

describe('Chart caching', function (): void {
    it('caches chart data when cacheTtl > 0', function (): void {
        $chart1 = livewire(LineChart::class, [
            'dataProvider' => TestDataProvider::class,
            'cacheTtl'     => 60,
        ])->instance();

        // Access chartData twice — should only call provider once (cached)
        $data1 = $chart1->chartData;
        $data2 = $chart1->chartData;

        expect($data1)->toBe($data2);
    });
});
