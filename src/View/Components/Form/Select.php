<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public string $sizeClass;

    /** @var array<int|string, string> */
    public array $options;

    public function __construct(
        public string $name = '',
        public ?string $label = null,
        public ?string $placeholder = null,
        public ?string $helper = null,
        public ?string $error = null,
        public bool $required = false,
        public bool $disabled = false,
        public bool $searchable = false,
        public ?string $searchUrl = null,
        public string $size = '',
        mixed $options = [],
    ) {
        $configSize = config('tallui.forms.size', 'md');
        $resolved   = $size ?: $configSize;

        $this->sizeClass = match ($resolved) {
            'xs'    => 'select-xs',
            'sm'    => 'select-sm',
            'lg'    => 'select-lg',
            default => 'select-md',
        };

        $this->options = is_array($options) ? $options : [];
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class(['form-control w-full', 'opacity-60' => $disabled])>
                @if($label)
                    <label @if($name) for="{{ $name }}" @endif class="label">
                        <span class="label-text font-medium">
                            {{ $label }}
                            @if($required) <span class="text-error ml-0.5">*</span> @endif
                        </span>
                    </label>
                @endif

                @if($searchable)
                    {{-- Async searchable select with Alpine.js --}}
                    <div
                        x-data="{
                            open: false,
                            search: '',
                            selected: null,
                            selectedLabel: '',
                            items: @js($options),
                            searchUrl: '{{ $searchUrl ?? route('tallui.select-search') }}',
                            async fetchItems() {
                                if (!this.searchUrl) return;
                                const res = await fetch(this.searchUrl + '&q=' + encodeURIComponent(this.search) + '&name={{ $name }}');
                                this.items = await res.json();
                            },
                            selectItem(item) {
                                this.selected = item.value;
                                this.selectedLabel = item.label;
                                this.search = item.label;
                                this.open = false;
                                this.$dispatch('input', item.value);
                            }
                        }"
                        class="relative"
                    >
                        <input
                            type="text"
                            x-model="search"
                            @focus="open = true"
                            @input.debounce.300ms="fetchItems()"
                            @click.away="open = false"
                            @keydown.escape="open = false"
                            @if($placeholder) placeholder="{{ $placeholder }}" @endif
                            @if($disabled) disabled @endif
                            class="input input-bordered w-full {{ $sizeClass }} @if($error) input-error @endif"
                        />
                        <input type="hidden" name="{{ $name }}" :value="selected" {{ $attributes->whereStartsWith('wire:model') }} />

                        <ul
                            x-show="open && items.length > 0"
                            x-cloak
                            class="absolute z-50 mt-1 w-full bg-base-100 border border-base-300 rounded-box shadow-lg max-h-60 overflow-auto"
                        >
                            <template x-for="item in items" :key="item.value">
                                <li
                                    @click="selectItem(item)"
                                    class="px-4 py-2 hover:bg-base-200 cursor-pointer text-sm"
                                    x-text="item.label"
                                ></li>
                            </template>
                        </ul>
                    </div>
                @else
                    {{-- Standard <select> --}}
                    <select
                        id="{{ $name }}"
                        name="{{ $name }}"
                        @if($required) required @endif
                        @if($disabled) disabled @endif
                        {{ $attributes->class([
                            'select select-bordered w-full',
                            $sizeClass,
                            'select-error' => $error,
                        ])->merge() }}
                    >
                        @if($placeholder)
                            <option value="" disabled selected>{{ $placeholder }}</option>
                        @endif

                        @foreach($options as $value => $optionLabel)
                            <option value="{{ $value }}">{{ $optionLabel }}</option>
                        @endforeach

                        {{ $slot }}
                    </select>
                @endif

                @if($error)
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $error }}</span>
                    </label>
                @elseif($helper)
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">{{ $helper }}</span>
                    </label>
                @endif
            </div>
            BLADE;
    }
}
