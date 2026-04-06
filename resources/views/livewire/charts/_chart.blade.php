<div
    wire:key="tallui-chart-{{ $this->getId() }}"
    @if($poll > 0) wire:poll.{{ $poll }}ms @endif
    x-data="tallUiChart(@js($this->buildOptions()), '{{ $this->getId() }}')"
    x-init="initChart()"
    x-on:livewire:updated.window="updateChart(@js($this->buildOptions()))"
    class="relative w-full"
>
    @assets
        <script src="{{ config('tallui.charts.apexcharts_cdn', 'https://cdn.jsdelivr.net/npm/apexcharts') }}"></script>
    @endassets

    @script
    <script>
        Alpine.data('tallUiChart', (options, id) => ({
            chart: null,

            initChart() {
                this.$nextTick(() => {
                    this.chart = new ApexCharts(this.$refs.chartEl, options);
                    this.chart.render();
                });
            },

            updateChart(newOptions) {
                if (this.chart) {
                    this.chart.updateOptions(newOptions, false, true);
                }
            },
        }));
    </script>
    @endscript

    {{-- Chart title --}}
    @if($title || $subtitle)
        <div class="mb-3 px-1">
            @if($title)
                <h3 class="text-sm font-semibold text-base-content">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-xs text-base-content/50 mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    {{-- Poll indicator --}}
    @if($poll > 0)
        <div class="absolute top-0 right-0 flex items-center gap-1 text-xs text-base-content/40">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-success animate-pulse"></span>
            Live
        </div>
    @endif

    <div wire:ignore x-ref="chartEl"></div>
</div>
