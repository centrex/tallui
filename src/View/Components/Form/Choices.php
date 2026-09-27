<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Centrex\TallUi\Concerns\HasUuid;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Choices extends Component
{
    use HasUuid;

    public function __construct(
        public string $name = '',
        public array $options = [],       // [['value' => '', 'label' => ''], ...] or flat ['val' => 'Label']
        public array $selected = [],      // pre-selected values
        public bool $multiple = true,
        public ?string $label = null,
        public ?string $placeholder = null,
        public ?string $error = null,
        public ?string $helper = null,
        public bool $searchable = true,
        public bool $required = false,
        public ?string $id = null,
    ) {
        $this->generateUuid($id);

        // Normalize options to [{value, label}] format
        $normalized = [];

        foreach ($options as $k => $v) {
            if (is_array($v)) {
                $normalized[] = $v;
            } else {
                $normalized[] = ['value' => $k, 'label' => $v];
            }
        }
        $this->options = $normalized;
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            @php
                // wire:model / wire:model.live → entangle `selected` with the Livewire property,
                // so the choice list works as a live filter, not just inside a submitted <form>.
                $wireModelKey = collect(array_keys($attributes->getAttributes()))
                    ->first(fn ($key) => str_starts_with((string) $key, 'wire:model'));
                $wireModel = $wireModelKey !== null ? $attributes->get($wireModelKey) : null;
                $wireLive = $wireModelKey !== null && str_contains((string) $wireModelKey, '.live');
            @endphp
            <div
                x-data="{
                    open: false,
                    search: '',
                    @if ($wireModel)
                        selected: $wire.entangle(@js($wireModel)){{ $wireLive ? '.live' : '' }},
                    @else
                        selected: {{ Js::from($selected) }},
                    @endif
                    options: {{ Js::from($options) }},
                    panelStyle: 'display:none',
                    get filtered() {
                        if (!this.search) return this.options;
                        const s = this.search.toLowerCase();
                        return this.options.filter(o => o.label.toLowerCase().includes(s));
                    },
                    {{-- `selected` may be an array, a scalar or null once entangled with Livewire; `values` is always a list of strings. --}}
                    get values() {
                        if (Array.isArray(this.selected)) return this.selected.map(String);
                        return this.selected === null || this.selected === undefined || this.selected === '' ? [] : [String(this.selected)];
                    },
                    setValues(values) {
                        this.selected = {{ $multiple ? 'true' : 'false' }} ? values : (values[0] ?? null);
                    },
                    isSelected(val) { return this.values.includes(String(val)); },
                    toggle(val) {
                        val = String(val);
                        if ({{ $multiple ? 'true' : 'false' }}) {
                            this.setValues(this.isSelected(val) ? this.values.filter(v => v !== val) : [...this.values, val]);
                        } else {
                            this.setValues(this.isSelected(val) ? [] : [val]);
                            this.open = false;
                        }
                    },
                    labelFor(val) {
                        const o = this.options.find(o => String(o.value) === String(val));
                        return o ? o.label : val;
                    },
                    remove(val) { this.setValues(this.values.filter(v => v !== String(val))); },
                    clear() { this.setValues([]); },
                    init() {
                        const reposition = () => { if (this.open) this.updatePanelPosition(); };
                        window.addEventListener('resize', reposition);
                        window.addEventListener('scroll', reposition, true);
                        document.addEventListener('mousedown', (event) => this.handleDocumentClick(event));
                    },
                    handleDocumentClick(event) {
                        if (!this.open) return;
                        const trigger = this.$refs.trigger;
                        const panel = this.$refs.panel;
                        if (trigger?.contains(event.target) || panel?.contains(event.target)) return;
                        this.open = false;
                    },
                    togglePanel() {
                        this.open ? (this.open = false) : this.openPanel();
                    },
                    openPanel() {
                        this.open = true;
                        this.$nextTick(() => this.updatePanelPosition());
                    },
                    updatePanelPosition() {
                        if (!this.open || !this.$refs.trigger) {
                            this.panelStyle = 'display:none';
                            return;
                        }

                        const rect = this.$refs.trigger.getBoundingClientRect();
                        const spacing = 4;
                        const preferredHeight = 260;
                        const spaceBelow = window.innerHeight - rect.bottom - spacing;
                        const spaceAbove = rect.top - spacing;
                        const openUpward = spaceBelow < preferredHeight && spaceAbove > spaceBelow;
                        const verticalStyle = openUpward
                            ? 'bottom:' + (window.innerHeight - rect.top + spacing) + 'px'
                            : 'top:' + (rect.bottom + spacing) + 'px';

                        this.panelStyle = [
                            'display:block',
                            verticalStyle,
                            'left:' + rect.left + 'px',
                            'width:' + rect.width + 'px',
                        ].join(';');
                    },
                }"
                class="form-control w-full"
            >
                @if($label)
                    <label class="label">
                        <span class="label-text font-medium">
                            {{ $label }}
                            @if($required) <span class="text-error ml-0.5">*</span> @endif
                        </span>
                    </label>
                @endif

                {{-- Trigger --}}
                <div
                    x-ref="trigger"
                    @click="togglePanel()"
                    role="combobox"
                    aria-haspopup="listbox"
                    :aria-expanded="open ? 'true' : 'false'"
                    @class([
                        'input input-bordered flex flex-wrap gap-1 items-center min-h-10 cursor-pointer relative',
                        'input-error' => $error,
                    ])
                >
                    {{-- Selected tags (multiple) --}}
                    @if($multiple)
                        <template x-for="val in values" :key="val">
                            <span class="badge badge-primary gap-1 shrink-0">
                                <span x-text="labelFor(val)"></span>
                                <button
                                    type="button"
                                    @click.stop="remove(val)"
                                    class="hover:text-error"
                                    aria-label="Remove"
                                >&times;</button>
                            </span>
                        </template>
                        <span
                            x-show="values.length === 0"
                            class="text-base-content/40 text-sm select-none"
                        >{{ $placeholder ?? __('Select options…') }}</span>
                    @else
                        <span
                            x-show="values.length > 0"
                            x-text="labelFor(values[0])"
                            class="text-sm"
                        ></span>
                        <span
                            x-show="values.length === 0"
                            class="text-base-content/40 text-sm select-none"
                        >{{ $placeholder ?? __('Select…') }}</span>
                    @endif

                    <button
                        type="button"
                        x-show="values.length > 1"
                        @click.stop="clear()"
                        class="ml-auto text-xs text-base-content/50 hover:text-error"
                        aria-label="{{ __('Clear all') }}"
                    >{{ __('Clear') }}</button>

                    {{-- Caret --}}
                    <x-tallui-icon name="o-chevron-down" class="w-4 h-4 ml-auto shrink-0 text-base-content/40 transition-transform duration-150" x-bind:class="open ? 'rotate-180' : ''" />
                </div>

                {{-- Dropdown (teleported to <body> so it can't be clipped by an overflow:hidden/transformed ancestor) --}}
                <template x-teleport="body">
                    <div
                        x-ref="panel"
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1 scale-y-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-y-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-y-100"
                        x-transition:leave-end="opacity-0 -translate-y-1 scale-y-95"
                        role="listbox"
                        :aria-multiselectable="{{ $multiple ? 'true' : 'false' }}"
                        class="fixed z-[9999] bg-base-100 border border-base-300 rounded-lg shadow-lg overflow-hidden"
                        :style="panelStyle"
                        style="display:none"
                    >
                        @if($searchable)
                            <div class="p-2 border-b border-base-200">
                                <input
                                    type="text"
                                    x-model="search"
                                    @click.stop
                                    placeholder="{{ __('Search…') }}"
                                    class="input input-sm input-bordered w-full"
                                    x-ref="searchInput"
                                    @focus="$refs.searchInput.focus()"
                                />
                            </div>
                        @endif

                        <ul class="max-h-52 overflow-y-auto py-1">
                            <template x-for="opt in filtered" :key="opt.value">
                                <li
                                    @click.stop="toggle(opt.value)"
                                    :class="isSelected(opt.value) ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-base-200'"
                                    role="option"
                                    :aria-selected="isSelected(opt.value) ? 'true' : 'false'"
                                    class="flex items-center gap-2 px-3 py-2 cursor-pointer text-sm select-none"
                                >
                                    <x-tallui-icon
                                        name="o-check"
                                        x-bind:class="isSelected(opt.value) ? 'text-primary' : 'text-base-content/20'"
                                        class="w-4 h-4 shrink-0"
                                    />
                                    <span x-text="opt.label"></span>
                                </li>
                            </template>
                            <li x-show="filtered.length === 0" class="px-3 py-4 text-sm text-center text-base-content/40">
                                {{ __('No results found') }}
                            </li>
                        </ul>
                    </div>
                </template>

                {{-- Hidden inputs for form submission --}}
                <template x-for="val in values" :key="val">
                    <input type="hidden" name="{{ $name }}{{ $multiple ? '[]' : '' }}" :value="val" />
                </template>

                @if($error)
                    <label class="label"><span class="label-text-alt text-error">{{ $error }}</span></label>
                @elseif($helper)
                    <label class="label"><span class="label-text-alt text-base-content/60">{{ $helper }}</span></label>
                @endif
            </div>
            BLADE;
    }
}
