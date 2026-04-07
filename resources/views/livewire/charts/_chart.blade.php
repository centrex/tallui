<div
    wire:key="tallui-chart-{{ $this->getId() }}"
    @if($poll > 0) wire:poll.{{ $poll }}ms @endif
    x-data="tallUiChart(@js($this->buildOptions()), '{{ $this->getId() }}')"
    x-init="initChart()"
    x-on:chart-updated.window="updateChart($event.detail.options)"
    class="relative w-full"
>
    @assets
        <script src="{{ config('tallui.charts.apexcharts_cdn', 'https://cdn.jsdelivr.net/npm/apexcharts') }}"></script>
    @endassets

    @script
    <script>
        Alpine.data('tallUiChart', (initialOptions, id) => ({
            chart: null,
            options: initialOptions,

            // ✅ Normalize options to prevent ApexCharts crashes
            normalizeOptions(opts) {
                opts = opts || {};

                // --- TITLE ---
                if (!opts.title) {
                    opts.title = { text: '', style: {} };
                } else {
                    opts.title.text = opts.title.text ?? '';
                    opts.title.style = opts.title.style || {};
                    opts.title.style.fontSize = opts.title.style.fontSize || '14px';
                }

                // --- SUBTITLE ---
                if (!opts.subtitle) {
                    opts.subtitle = { text: '', style: {} };
                } else {
                    opts.subtitle.text = opts.subtitle.text ?? '';
                    opts.subtitle.style = opts.subtitle.style || {};
                }

                // --- CHART ---
                opts.chart = opts.chart || {};
                opts.chart.type = opts.chart.type || 'line';

                // --- SERIES ---
                opts.series = Array.isArray(opts.series) ? opts.series : [];

                // --- XAXIS ---
                opts.xaxis = opts.xaxis || {};
                opts.xaxis.categories = opts.xaxis.categories || [];

                return opts;
            },

            initChart() {
                this.$nextTick(() => {
                    const safeOptions = this.normalizeOptions(this.options);

                    // Destroy if already exists (Livewire safety)
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    this.chart = new ApexCharts(this.$refs.chartEl, safeOptions);
                    this.chart.render();
                });
            },

            updateChart(newOptions) {
                if (!this.chart) return;

                const safeOptions = this.normalizeOptions(newOptions);

                try {
                    this.chart.updateOptions(safeOptions, true, true);
                } catch (e) {
                    console.warn('Chart update failed, re-initializing...', e);

                    // fallback hard reset
                    this.chart.destroy();
                    this.chart = new ApexCharts(this.$refs.chartEl, safeOptions);
                    this.chart.render();
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