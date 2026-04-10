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
            <div
                x-data="{
                    open: false,
                    search: '',
                    selected: {{ Js::from($selected) }},
                    options: {{ Js::from($options) }},
                    get filtered() {
                        if (!this.search) return this.options;
                        const s = this.search.toLowerCase();
                        return this.options.filter(o => o.label.toLowerCase().includes(s));
                    },
                    isSelected(val) { return this.selected.includes(String(val)); },
                    toggle(val) {
                        val = String(val);
                        if ({{ $multiple ? 'true' : 'false' }}) {
                            this.isSelected(val) ? this.selected.splice(this.selected.indexOf(val), 1) : this.selected.push(val);
                        } else {
                            this.selected = this.isSelected(val) ? [] : [val];
                            this.open = false;
                        }
                    },
                    labelFor(val) {
                        const o = this.options.find(o => String(o.value) === String(val));
                        return o ? o.label : val;
                    },
                    remove(val) { this.selected.splice(this.selected.indexOf(String(val)), 1); },
                }"
                @click.outside="open = false"
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
                    @click="open = !open"
                    @class([
                        'input input-bordered flex flex-wrap gap-1 items-center min-h-10 cursor-pointer relative',
                        'input-error' => $error,
                    ])
                >
                    {{-- Selected tags (multiple) --}}
                    @if($multiple)
                        <template x-for="val in selected" :key="val">
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
                            x-show="selected.length === 0"
                            class="text-base-content/40 text-sm select-none"
                        >{{ $placeholder ?? __('Select options…') }}</span>
                    @else
                        <span
                            x-show="selected.length > 0"
                            x-text="labelFor(selected[0])"
                            class="text-sm"
                        ></span>
                        <span
                            x-show="selected.length === 0"
                            class="text-base-content/40 text-sm select-none"
                        >{{ $placeholder ?? __('Select…') }}</span>
                    @endif

                    {{-- Caret --}}
                    <x-tallui-icon name="o-chevron-down" class="w-4 h-4 ml-auto shrink-0 text-base-content/40 transition-transform duration-150" :class="open ? 'rotate-180' : ''" />
                </div>

                {{-- Dropdown --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 -translate-y-1 scale-y-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-y-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-y-100"
                    x-transition:leave-end="opacity-0 -translate-y-1 scale-y-95"
                    class="absolute z-50 mt-1 w-full bg-base-100 border border-base-300 rounded-lg shadow-lg overflow-hidden"
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
                                class="flex items-center gap-2 px-3 py-2 cursor-pointer text-sm select-none"
                            >
                                <x-tallui-icon
                                    name="o-check"
                                    :class="isSelected(opt.value) ? 'text-primary' : 'text-base-content/20'"
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

                {{-- Hidden inputs for form submission --}}
                <template x-for="val in selected" :key="val">
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
