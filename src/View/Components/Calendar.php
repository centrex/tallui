<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Calendar extends Component
{
    public function __construct(
        public ?string $selected = null, // YYYY-MM-DD
        public ?string $month = null, // YYYY-MM, defaults to current
        public array $events = [], // [['date'=>'YYYY-MM-DD','label'=>'','color'=>'primary'?]]
        public bool $selectable = true,
        public ?string $wire = null, // wire:model property name
    ) {
        $this->month ??= now()->format('Y-m');
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    year:     {{ (int) explode('-', $month)[0] }},
                    month:    {{ (int) explode('-', $month)[1] }},
                    selected: '{{ $selected ?? '' }}',
                    events:   {{ Js::from($events) }},
                    selectable: {{ $selectable ? 'true' : 'false' }},

                    get monthName() {
                        return new Date(this.year, this.month - 1, 1)
                            .toLocaleString('default', { month: 'long', year: 'numeric' });
                    },
                    get days() {
                        const first = new Date(this.year, this.month - 1, 1).getDay();
                        const total = new Date(this.year, this.month, 0).getDate();
                        const blanks = Array(first).fill(null);
                        const days   = Array.from({ length: total }, (_, i) => i + 1);
                        return [...blanks, ...days];
                    },
                    dateStr(d) {
                        return `${this.year}-${String(this.month).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                    },
                    eventsFor(d) {
                        return this.events.filter(e => e.date === this.dateStr(d));
                    },
                    select(d) {
                        if (!this.selectable || !d) return;
                        this.selected = this.dateStr(d);
                        @if($wire) $wire.set('{{ $wire }}', this.selected); @endif
                        $dispatch('calendar-select', { date: this.selected });
                    },
                    prevMonth() {
                        if (--this.month < 1) { this.month = 12; this.year--; }
                    },
                    nextMonth() {
                        if (++this.month > 12) { this.month = 1; this.year++; }
                    },
                    isToday(d) {
                        return this.dateStr(d) === new Date().toISOString().split('T')[0];
                    },
                    isSelected(d) { return d && this.selected === this.dateStr(d); },
                }"
                class="select-none"
                {{ $attributes }}
            >
                {{-- Header --}}
                <div class="flex items-center justify-between mb-3">
                    <button @click="prevMonth()" class="btn btn-ghost btn-sm btn-square">‹</button>
                    <span class="font-semibold text-sm" x-text="monthName"></span>
                    <button @click="nextMonth()" class="btn btn-ghost btn-sm btn-square">›</button>
                </div>

                {{-- Day headers --}}
                <div class="grid grid-cols-7 mb-1">
                    @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $day)
                        <div class="text-center text-xs font-medium text-base-content/50 py-1">{{ $day }}</div>
                    @endforeach
                </div>

                {{-- Days grid --}}
                <div class="grid grid-cols-7 gap-y-1">
                    <template x-for="(day, i) in days" :key="i">
                        <div
                            @click="select(day)"
                            :class="{
                                'cursor-pointer hover:bg-base-200': day && selectable,
                                'cursor-default': !day || !selectable,
                                'rounded-full bg-primary text-primary-content': isSelected(day),
                                'ring-1 ring-primary ring-offset-1': isToday(day) && !isSelected(day),
                            }"
                            class="relative flex flex-col items-center justify-start pt-1 pb-0.5 min-h-[2.5rem] rounded-lg transition-colors"
                        >
                            <span
                                class="text-sm leading-none"
                                :class="{ 'opacity-0': !day }"
                                x-text="day || ''"
                            ></span>

                            {{-- Event dots --}}
                            <div class="flex gap-0.5 mt-0.5 flex-wrap justify-center">
                                <template x-for="ev in eventsFor(day)" :key="ev.label">
                                    <span
                                        :title="ev.label"
                                        :class="`bg-${ev.color || 'primary'}`"
                                        class="w-1 h-1 rounded-full"
                                    ></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Selected date display --}}
                @if($selectable)
                    <div class="mt-3 text-xs text-base-content/50 text-center" x-show="selected" x-text="`Selected: ${selected}`"></div>
                @endif
            </div>
            BLADE;
    }
}
