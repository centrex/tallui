<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Select extends Component
{
    public string $sizeClass;

    /** @var array<int|string, string> */
    public array $options;

    /** @var array<int, array{value:mixed,label:string,sublabel:?string}> */
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
        public array $searchSource = [],
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
            uasort($this->options, function (mixed $left, mixed $right): int {
                $leftLabel = is_array($left)
                    ? (string) ($left['label'] ?? $left['value'] ?? '')
                    : (string) $left;
                $rightLabel = is_array($right)
                    ? (string) ($right['label'] ?? $right['value'] ?? '')
                    : (string) $right;

                return strnatcasecmp($leftLabel, $rightLabel);
            });
        }

        $this->normalizedOptions = $this->normalizeOptions($this->options);

        $this->resolvedSearchUrl = $this->resolveSearchUrl();
        $this->isAsyncSearch = $this->searchable && filled($this->resolvedSearchUrl);
    }

    /**
     * @param  array<int|string, mixed>  $options
     * @return array<int, array{value:mixed,label:string,sublabel:?string}>
     */
    protected function normalizeOptions(array $options): array
    {
        return collect($options)
            ->map(function (mixed $label, mixed $value): array {
                if (is_array($label) && array_key_exists('value', $label) && array_key_exists('label', $label)) {
                    return [
                        'value'    => $label['value'],
                        'label'    => (string) $label['label'],
                        'sublabel' => isset($label['sublabel']) && $label['sublabel'] !== ''
                            ? (string) $label['sublabel']
                            : null,
                    ];
                }

                if (is_array($label) && array_key_exists('label', $label)) {
                    return [
                        'value'    => $value,
                        'label'    => (string) $label['label'],
                        'sublabel' => isset($label['sublabel']) && $label['sublabel'] !== ''
                            ? (string) $label['sublabel']
                            : null,
                    ];
                }

                return [
                    'value'    => $value,
                    'label'    => (string) $label,
                    'sublabel' => null,
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

        if ($this->searchSource !== [] && function_exists('route')) {
            $token = Str::random(40);
            $ttl = max(60, (int) config('tallui.forms.search_source_ttl', 1800));

            Cache::put("tallui:select-source:{$token}", $this->searchSource, $ttl);

            return route('tallui.select-search', ['source' => $token]);
        }

        return null;
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
<div @class(['form-control w-full', 'opacity-60 pointer-events-none' => $disabled])>
    @php
        $inputId = $name !== '' ? $name : uniqid('select_', false);
        $hiddenInputAttributes = collect($attributes->getAttributes())
            ->filter(fn ($value, $key) => str_starts_with((string) $key, 'wire:') || str_starts_with((string) $key, 'x-') || str_starts_with((string) $key, '@'))
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
                @foreach($hiddenInputAttributes as $wireKey => $wireValue)
                    {{ $wireKey }}="{{ $wireValue }}"
                @endforeach
            />

            <template x-teleport="body">
                <div
                    x-ref="dropdownPanel"
                    x-show="open"
                    x-cloak
                    @click.stop
                    class="fixed z-[9999] bg-base-100 border border-base-300 rounded-box shadow-lg"
                    :style="panelStyle"
                    style="display:none"
                >
                    <ul
                        x-show="items.length > 0"
                        x-ref="itemsList"
                        @scroll="handleListScroll()"
                        class="max-h-60 overflow-auto"
                    >
                        <template x-for="(item, index) in items" :key="String(item.value)">
                            <li
                                @click="selectItem(item)"
                                @mouseenter="highlightedIndex = index"
                                :class="{
                                    'bg-base-200': highlightedIndex === index,
                                    'font-medium': String(selected) === String(item.value)
                                }"
                                class="px-4 py-2 cursor-pointer"
                            >
                                <div class="text-sm leading-tight" x-text="item.label"></div>
                                <div
                                    x-show="item.sublabel"
                                    class="mt-0.5 text-xs leading-tight text-base-content/60"
                                    x-text="item.sublabel"
                                ></div>
                            </li>
                        </template>
                    </ul>

                    <div
                        x-show="!loading && items.length === 0 && search.length > 0"
                        class="px-4 py-2 text-sm text-base-content/70"
                    >
                        No results found.
                    </div>

                    <div
                        x-show="loading"
                        class="px-4 py-2 text-sm text-base-content/70"
                    >
                        Searching...
                    </div>

                    <div
                        x-show="loadingMore"
                        class="px-4 py-2 text-sm text-base-content/70"
                    >
                        Loading more...
                    </div>
                </div>
            </template>
        </div>

        <script>
            function selectComponent(config) {
                return {
                    instanceId: 'select-' + Math.random().toString(36).slice(2),
                    open: false,
                    loading: false,
                    loadingMore: false,
                    search: '',
                    selected: config.initialValue ?? '',
                    items: Array.isArray(config.initialItems) ? config.initialItems : [],
                    allItems: Array.isArray(config.initialItems) ? config.initialItems : [],
                    selectedLabel: '',
                    highlightedIndex: -1,
                    panelStyle: 'display:none',
                    placeholder: config.placeholder ?? '',
                    searchUrl: config.searchUrl ?? null,
                    asyncMode: Boolean(config.asyncMode),
                    disabled: Boolean(config.disabled),
                    required: Boolean(config.required),
                    page: 1,
                    hasMore: true,
                    lastSearch: '',

                    init() {
                        this.syncLabelFromSelected();
                        this.updatePanelPosition();

                        const reposition = () => {
                            if (this.open) {
                                this.updatePanelPosition();
                            }
                        };

                        window.addEventListener('resize', reposition);
                        window.addEventListener('scroll', reposition, true);

                        window.addEventListener('tallui-select-opened', (event) => {
                            if (event.detail?.instanceId !== this.instanceId) {
                                this.close();
                            }
                        });

                        document.addEventListener('mousedown', (event) => {
                            this.handleDocumentClick(event);
                        });
                    },

                    handleFocus() {
                        if (this.disabled) {
                            return;
                        }

                        this.open = true;
                        window.dispatchEvent(new CustomEvent('tallui-select-opened', { detail: { instanceId: this.instanceId } }));
                        this.updatePanelPosition();

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
                        window.dispatchEvent(new CustomEvent('tallui-select-opened', { detail: { instanceId: this.instanceId } }));
                        this.highlightedIndex = -1;
                        this.updatePanelPosition();

                        if (!this.asyncMode || !this.searchUrl) {
                            this.items = this.filteredLocalItems();
                            this.highlightedIndex = this.items.length > 0 ? 0 : -1;
                            return;
                        }

                        this.loading = true;
                        this.page = 1;
                        this.hasMore = true;
                        this.lastSearch = this.search ?? '';

                        try {
                            const url = new URL(this.searchUrl, window.location.origin);
                            url.searchParams.set('q', this.search ?? '');
                            url.searchParams.set('page', String(this.page));

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
                            const normalizedItems = Array.isArray(payload)
                                ? this.normalizeItems(payload)
                                : Array.isArray(payload?.data)
                                    ? this.normalizeItems(payload.data)
                                    : [];

                            this.items = normalizedItems;
                            this.allItems = this.mergeItems(this.allItems, normalizedItems);
                            this.hasMore = Array.isArray(payload)
                                ? normalizedItems.length > 0
                                : Boolean(payload?.has_more);

                            this.highlightedIndex = this.items.length > 0 ? 0 : -1;
                        } catch (error) {
                            console.error('Async select search failed:', error);
                            this.items = [];
                        } finally {
                            this.loading = false;
                        }
                    },

                    async loadMore() {
                        if (!this.asyncMode || !this.searchUrl || this.loading || this.loadingMore || !this.hasMore) {
                            return;
                        }

                        this.loadingMore = true;

                        try {
                            const nextPage = this.page + 1;
                            const url = new URL(this.searchUrl, window.location.origin);
                            url.searchParams.set('q', this.lastSearch ?? this.search ?? '');
                            url.searchParams.set('page', String(nextPage));

                            const response = await fetch(url.toString(), {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            if (!response.ok) {
                                this.hasMore = false;
                                return;
                            }

                            const payload = await response.json();
                            const normalizedItems = Array.isArray(payload)
                                ? this.normalizeItems(payload)
                                : Array.isArray(payload?.data)
                                    ? this.normalizeItems(payload.data)
                                    : [];

                            this.items = this.mergeItems(this.items, normalizedItems);
                            this.allItems = this.mergeItems(this.allItems, normalizedItems);
                            this.page = nextPage;
                            this.hasMore = Array.isArray(payload)
                                ? normalizedItems.length > 0
                                : Boolean(payload?.has_more);
                        } catch (error) {
                            console.error('Async select load more failed:', error);
                            this.hasMore = false;
                        } finally {
                            this.loadingMore = false;
                        }
                    },

                    handleListScroll() {
                        if (!this.$refs.itemsList) {
                            return;
                        }

                        const threshold = 32;
                        const remaining = this.$refs.itemsList.scrollHeight
                            - this.$refs.itemsList.scrollTop
                            - this.$refs.itemsList.clientHeight;

                        if (remaining <= threshold) {
                            this.loadMore();
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
                                sublabel: Object.prototype.hasOwnProperty.call(item, 'sublabel') && item.sublabel !== null && item.sublabel !== ''
                                    ? String(item.sublabel)
                                    : null,
                            }))
                            .filter(item => item.label !== '');
                    },

                    mergeItems(...itemSets) {
                        const mergedItems = [];
                        const seenValues = new Set();

                        itemSets
                            .flat()
                            .forEach((item) => {
                                if (!item || typeof item !== 'object') {
                                    return;
                                }

                                const normalizedItem = {
                                    value: Object.prototype.hasOwnProperty.call(item, 'value') ? item.value : '',
                                    label: Object.prototype.hasOwnProperty.call(item, 'label') ? String(item.label) : '',
                                    sublabel: Object.prototype.hasOwnProperty.call(item, 'sublabel') && item.sublabel !== null && item.sublabel !== ''
                                        ? String(item.sublabel)
                                        : null,
                                };

                                if (normalizedItem.label === '') {
                                    return;
                                }

                                const itemKey = String(normalizedItem.value);

                                if (seenValues.has(itemKey)) {
                                    return;
                                }

                                seenValues.add(itemKey);
                                mergedItems.push(normalizedItem);
                            });

                        return mergedItems;
                    },

                    syncLabelFromSelected() {
                        if (this.selected === null || this.selected === undefined || this.selected === '') {
                            this.selectedLabel = '';
                            this.search = '';
                            return;
                        }

                        const selectedItem = this.allItems.find(
                            item => String(item.value) === String(this.selected)
                        );

                        if (selectedItem) {
                            this.selectedLabel = selectedItem.label;
                        }

                        this.search = this.selectedLabel;
                    },

                    selectItem(item) {
                        this.allItems = this.mergeItems([item], this.allItems);
                        this.selected = item.value;
                        this.selectedLabel = item.label;
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

                    handleDocumentClick(event) {
                        if (!this.open) {
                            return;
                        }

                        const input = this.$refs.textInput;
                        const panel = this.$refs.dropdownPanel;
                        const target = event.target;

                        if (input?.contains(target) || panel?.contains(target)) {
                            return;
                        }

                        this.close();
                    },

                    updatePanelPosition() {
                        if (!this.open || !this.$refs.textInput) {
                            this.panelStyle = 'display:none';
                            return;
                        }

                        const rect = this.$refs.textInput.getBoundingClientRect();
                        const spacing = 4;

                        this.panelStyle = [
                            'display:block',
                            'left:' + rect.left + 'px',
                            'top:' + (rect.bottom + spacing) + 'px',
                            'width:' + rect.width + 'px',
                        ].join(';');
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
                    @if(is_array($optionLabel) && array_key_exists('label', $optionLabel))
                        {{ $optionLabel['label'] }}@if(filled($optionLabel['sublabel'] ?? null)) - {{ $optionLabel['sublabel'] }} @endif
                    @else
                        {{ $optionLabel }}
                    @endif
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
