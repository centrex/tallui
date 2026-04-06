<div
    wire:key="tallui-chart-{{ $this->getId() }}"
    @if($poll > 0) wire:poll.{{ $poll }}ms @endif
    x-data="tallUiChart(@js($this->buildOptions()), '{{ $this->id }}')"
    x-init="initChart()"
    x-on:livewire:updated.window="updateChart(@js($this->buildOptions()))"
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

    <div wire:ignore x-ref="chartEl"></div>
</div>
