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

    /** @var array<int, array{value:mixed,label:string}> */
    public array $normalizedOptions;

    public bool $isAsyncSearch;

    public ?string $resolvedSearchUrl;

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
        public ?string $searchName = null,
        public bool $sort = true,
        public string $size = '',
        mixed $options = [],
    ) {
        $configSize = config('tallui.forms.size', 'md');
        $resolvedSize = $size !== '' ? $size : $configSize;

        $this->sizeClass = match ($resolvedSize) {
            'xs'    => 'select-xs',
            'sm'    => 'select-sm',
            'lg'    => 'select-lg',
            default => 'select-md',
        };

        $this->options = is_array($options) ? $options : [];

        if ($this->sort) {
            asort($this->options, SORT_NATURAL | SORT_FLAG_CASE);
        }

        $this->normalizedOptions = $this->normalizeOptions($this->options);

        $this->resolvedSearchUrl = $this->resolveSearchUrl();
        $this->isAsyncSearch = $this->searchable && filled($this->resolvedSearchUrl);
    }

    /**
     * @param  array<int|string, mixed>  $options
     * @return array<int, array{value:mixed,label:string}>
     */
    protected function normalizeOptions(array $options): array
    {
        return collect($options)
            ->map(function (mixed $label, mixed $value): array {
                if (is_array($label) && array_key_exists('value', $label) && array_key_exists('label', $label)) {
                    return [
                        'value' => $label['value'],
                        'label' => (string) $label['label'],
                    ];
                }

                return [
                    'value' => $value,
                    'label' => (string) $label,
                ];
            })
            ->values()
            ->all();
    }

    protected function resolveSearchUrl(): ?string
    {
        if (filled($this->searchUrl)) {
            return $this->searchUrl;
        }

        if (filled($this->searchName) && function_exists('route')) {
            return route('tallui.select-search', ['name' => $this->searchName]);
        }

        return null;
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
<div @class(['form-control w-full', 'opacity-60 pointer-events-none' => $disabled])>
    @php
        $inputId = $name !== '' ? $name : uniqid('select_', false);
        $wireModelAttribute = collect($attributes->getAttributes())
            ->filter(fn ($value, $key) => str_starts_with((string) $key, 'wire:model'))
            ->mapWithKeys(fn ($value, $key) => [$key => $value]);

        $currentValue = old($name, $attributes->get('value'));
    @endphp

    @if($label)
        <label @if($name) for="{{ $inputId }}" @endif class="label">
            <span class="label-text font-medium">
                {{ $label }}
                @if($required)
                    <span class="text-error ml-0.5">*</span>
                @endif
            </span>
        </label>
    @endif

    @if($searchable)
        <div
            x-data="selectComponent({
                initialValue: @js($currentValue),
                initialItems: @js($normalizedOptions),
                placeholder: @js($placeholder),
                searchUrl: @js($resolvedSearchUrl),
                asyncMode: @js($isAsyncSearch),
                disabled: @js($disabled),
                required: @js($required),
            })"
            x-init="init()"
            class="relative"
        >
            <input
                id="{{ $inputId }}"
                x-ref="textInput"
                type="text"
                x-model="search"
                @focus="handleFocus()"
                @input.debounce.300ms="handleSearch()"
                @keydown.arrow-down.prevent="highlightNext()"
                @keydown.arrow-up.prevent="highlightPrevious()"
                @keydown.enter.prevent="selectHighlighted()"
                @keydown.escape="close()"
                @click.outside="close()"
                @if($placeholder) placeholder="{{ $placeholder }}" @endif
                @if($disabled) disabled @endif
                autocomplete="off"
                class="input input-bordered w-full {{ $sizeClass }} @if($error) input-error @endif"
            />

            <input
                x-ref="hiddenInput"
                type="hidden"
                name="{{ $name }}"
                x-model="selected"
                @foreach($wireModelAttribute as $wireKey => $wireValue)
                    {{ $wireKey }}="{{ $wireValue }}"
                @endforeach
            />

            <ul
                x-show="open && items.length > 0"
                x-cloak
                class="absolute z-50 mt-1 w-full bg-base-100 border border-base-300 rounded-box shadow-lg max-h-60 overflow-auto"
            >
                <template x-for="(item, index) in items" :key="String(item.value)">
                    <li
                        @click="selectItem(item)"
                        @mouseenter="highlightedIndex = index"
                        :class="{
                            'bg-base-200': highlightedIndex === index,
                            'font-medium': String(selected) === String(item.value)
                        }"
                        class="px-4 py-2 cursor-pointer text-sm"
                        x-text="item.label"
                    ></li>
                </template>
            </ul>

            <div
                x-show="open && !loading && items.length === 0 && search.length > 0"
                x-cloak
                class="absolute z-50 mt-1 w-full bg-base-100 border border-base-300 rounded-box shadow-lg px-4 py-2 text-sm text-base-content/70"
            >
                No results found.
            </div>

            <div
                x-show="open && loading"
                x-cloak
                class="absolute z-50 mt-1 w-full bg-base-100 border border-base-300 rounded-box shadow-lg px-4 py-2 text-sm text-base-content/70"
            >
                Searching...
            </div>
        </div>

        <script>
            function selectComponent(config) {
                return {
                    open: false,
                    loading: false,
                    search: '',
                    selected: config.initialValue ?? '',
                    items: Array.isArray(config.initialItems) ? config.initialItems : [],
                    allItems: Array.isArray(config.initialItems) ? config.initialItems : [],
                    highlightedIndex: -1,
                    placeholder: config.placeholder ?? '',
                    searchUrl: config.searchUrl ?? null,
                    asyncMode: Boolean(config.asyncMode),
                    disabled: Boolean(config.disabled),
                    required: Boolean(config.required),

                    init() {
                        this.syncLabelFromSelected();
                    },

                    handleFocus() {
                        if (this.disabled) {
                            return;
                        }

                        this.open = true;

                        if (this.asyncMode) {
                            this.handleSearch();
                            return;
                        }

                        this.items = this.filteredLocalItems();
                        this.highlightedIndex = this.items.length > 0 ? 0 : -1;
                    },

                    async handleSearch() {
                        if (this.disabled) {
                            return;
                        }

                        this.open = true;
                        this.highlightedIndex = -1;

                        if (!this.asyncMode || !this.searchUrl) {
                            this.items = this.filteredLocalItems();
                            this.highlightedIndex = this.items.length > 0 ? 0 : -1;
                            return;
                        }

                        this.loading = true;

                        try {
                            const url = new URL(this.searchUrl, window.location.origin);
                            url.searchParams.set('q', this.search ?? '');

                            const response = await fetch(url.toString(), {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            if (!response.ok) {
                                this.items = [];
                                return;
                            }

                            const payload = await response.json();

                            if (Array.isArray(payload)) {
                                this.items = this.normalizeItems(payload);
                            } else if (Array.isArray(payload?.data)) {
                                this.items = this.normalizeItems(payload.data);
                            } else {
                                this.items = [];
                            }

                            this.highlightedIndex = this.items.length > 0 ? 0 : -1;
                        } catch (error) {
                            console.error('Async select search failed:', error);
                            this.items = [];
                        } finally {
                            this.loading = false;
                        }
                    },

                    filteredLocalItems() {
                        const term = String(this.search ?? '').trim().toLowerCase();

                        if (term === '') {
                            return [...this.allItems];
                        }

                        return this.allItems.filter((item) =>
                            String(item.label).toLowerCase().includes(term)
                        );
                    },

                    normalizeItems(items) {
                        return items
                            .filter(item => item && typeof item === 'object')
                            .map(item => ({
                                value: Object.prototype.hasOwnProperty.call(item, 'value') ? item.value : '',
                                label: Object.prototype.hasOwnProperty.call(item, 'label') ? String(item.label) : '',
                            }))
                            .filter(item => item.label !== '');
                    },

                    syncLabelFromSelected() {
                        if (this.selected === null || this.selected === undefined || this.selected === '') {
                            this.search = '';
                            return;
                        }

                        const selectedItem = this.allItems.find(
                            item => String(item.value) === String(this.selected)
                        );

                        this.search = selectedItem ? selectedItem.label : '';
                    },

                    selectItem(item) {
                        this.selected = item.value;
                        this.search = item.label;
                        this.open = false;
                        this.highlightedIndex = -1;
                        this.syncHiddenInput();
                    },

                    syncHiddenInput() {
                        this.$nextTick(() => {
                            if (!this.$refs.hiddenInput) {
                                return;
                            }

                            this.$refs.hiddenInput.value = this.selected ?? '';
                            this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                            this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    },

                    highlightNext() {
                        if (!this.open || this.items.length === 0) {
                            return;
                        }

                        this.highlightedIndex = this.highlightedIndex < this.items.length - 1
                            ? this.highlightedIndex + 1
                            : 0;
                    },

                    highlightPrevious() {
                        if (!this.open || this.items.length === 0) {
                            return;
                        }

                        this.highlightedIndex = this.highlightedIndex > 0
                            ? this.highlightedIndex - 1
                            : this.items.length - 1;
                    },

                    selectHighlighted() {
                        if (!this.open || this.highlightedIndex < 0 || !this.items[this.highlightedIndex]) {
                            return;
                        }

                        this.selectItem(this.items[this.highlightedIndex]);
                    },

                    close() {
                        this.open = false;
                        this.highlightedIndex = -1;
                        this.syncLabelFromSelected();
                    },
                };
            }
        </script>
    @else
        <select
            id="{{ $inputId }}"
            name="{{ $name }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->class([
                'select select-bordered w-full',
                $sizeClass,
                'select-error' => $error,
            ])->except(['value']) }}
        >
            @if($placeholder)
                <option value="" @selected($currentValue === null || $currentValue === '')>
                    {{ $placeholder }}
                </option>
            @endif

            @foreach($options as $value => $optionLabel)
                <option value="{{ $value }}" @selected((string) $currentValue === (string) $value)>
                    {{ $optionLabel }}
                </option>
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
