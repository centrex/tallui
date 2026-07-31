<div
    wire:key="tallui-chart-{{ $this->getId() }}"
    @if($poll > 0) wire:poll.{{ $poll }}ms @endif
    x-data="tallUiChart(@js($this->buildOptions()), '{{ $this->getId() }}')"
    x-init="initChart()"
    x-on:chart-updated.window="$event.detail.id === id && updateChart($event.detail.options)"
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

            // Server-side options are JSON-encoded, so any ApexCharts `formatter`
            // callbacks arrive as inert strings, not callables. Revive any string
            // that looks like a function expression into a real function.
            reviveFunctions(value) {
                if (Array.isArray(value)) {
                    return value.map((v) => this.reviveFunctions(v));
                }

                if (value && typeof value === 'object') {
                    const out = {};

                    for (const [key, v] of Object.entries(value)) {
                        out[key] = this.reviveFunctions(v);
                    }

                    return out;
                }

                if (typeof value === 'string' && /^\s*function\s*\(/.test(value)) {
                    try {
                        return new Function('return (' + value + ')')();
                    } catch (e) {
                        console.warn('Failed to revive chart formatter function', e);

                        return value;
                    }
                }

                return value;
            },

            // ✅ Normalize options to prevent ApexCharts crashes
            normalizeOptions(opts) {
                opts = this.reviveFunctions(opts || {});

                // --- TITLE ---
                if (!opts.title || Array.isArray(opts.title)) {
                    opts.title = { text: '', style: {} };
                } else {
                    opts.title.text  = opts.title.text  ?? '';
                    opts.title.style = opts.title.style || {};
                    opts.title.style.fontSize = opts.title.style.fontSize || '14px';
                }

                // --- SUBTITLE ---
                if (!opts.subtitle || Array.isArray(opts.subtitle)) {
                    opts.subtitle = { text: '', style: {} };
                } else {
                    opts.subtitle.text  = opts.subtitle.text  ?? '';
                    opts.subtitle.style = opts.subtitle.style || {};
                }

                // --- CHART ---
                opts.chart      = opts.chart || {};
                opts.chart.type = opts.chart.type || 'line';

                const type = opts.chart.type;

                // --- SERIES ---
                opts.series = Array.isArray(opts.series) ? opts.series : [];

                // --- XAXIS / LABELS (type-aware) ---
                // Polar-family charts (pie, donut, radialBar, polarArea) use `labels`, not `xaxis`.
                const polarTypes = ['pie', 'donut', 'radialBar', 'polarArea'];
                if (polarTypes.includes(type)) {
                    opts.labels = Array.isArray(opts.labels) ? opts.labels : [];
                } else if (type === 'treemap') {
                    // Treemap encodes categories inside series[].data[].x — no xaxis needed.
                } else if (type === 'rangeArea') {
                    // RangeArea encodes x inside each data point — xaxis is optional.
                    opts.xaxis = opts.xaxis || {};
                } else {
                    opts.xaxis            = opts.xaxis || {};
                    opts.xaxis.categories = opts.xaxis.categories || [];
                }

                // --- MIXED: ensure every series has a `type` key ---
                // Only applies when at least one series already declares a type (mixed chart
                // disguised as chart.type='line'). Pure line charts have no per-series type.
                const isMultiType = Array.isArray(opts.series) && opts.series.some(s => s && s.type);
                if (type === 'line' && isMultiType) {
                    opts.series = opts.series.map(s =>
                        s && typeof s === 'object' && !s.type ? { ...s, type: 'bar' } : s
                    );
                }

                return opts;
            },

            initChart() {
                this.$nextTick(() => {
                    const safeOptions = this.normalizeOptions(this.options);

                    // Destroy if already exists (Livewire safety)
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    // chartEl is wire:ignore'd, so Livewire never clears its contents on
                    // its own — if this Alpine component gets re-initialized (e.g. the
                    // surrounding wire:key'd element is replaced rather than morphed in
                    // place across a re-render), `this.chart` above resets to null and the
                    // destroy() guard is skipped even though the *previous* chart's SVG is
                    // still sitting in the DOM. Clearing the container unconditionally
                    // before rendering guarantees only one chart is ever visible, whatever
                    // caused this to run again.
                    this.$refs.chartEl.replaceChildren();

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

                    // fallback hard reset — same wire:ignore leftover-DOM hazard as
                    // initChart() above: destroy() isn't guaranteed to leave chartEl
                    // empty, so clear it explicitly before rendering the replacement.
                    this.chart.destroy();
                    this.$refs.chartEl.replaceChildren();
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