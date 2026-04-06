<div wire:key="tallui-datatable-{{ $this->getId() }}">

    {{-- ═══════════════════════════════════════════════════
         TOOLBAR  (search · filter toggle · per-page)
    ═══════════════════════════════════════════════════ --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">

        {{-- Async search --}}
        <div class="flex-1 min-w-[200px] max-w-sm">
            <label class="input input-bordered flex items-center gap-2">
                {{-- Search icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 opacity-50" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>

                <input
                    type="search"
                    wire:model.live.debounce.400ms="search"
                    placeholder="{{ $minSearchLength > 0 ? 'Type ' . $minSearchLength . '+ chars…' : 'Search…' }}"
                    class="grow bg-transparent outline-none border-none focus:ring-0 text-sm"
                    autocomplete="off"
                />

                {{-- Spinner shown while Livewire is processing the search --}}
                <span wire:loading wire:target="search" class="loading loading-spinner loading-xs shrink-0"></span>

                {{-- Clear button — shown only when search has a value --}}
                @if($search !== '')
                    <button
                        wire:click="clearSearch"
                        class="btn btn-ghost btn-xs btn-circle shrink-0"
                        title="Clear search"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                @endif

                {{-- Min-length hint --}}
                @if($minSearchLength > 0 && $search !== '' && mb_strlen($search) < $minSearchLength)
                    <span class="text-xs text-warning shrink-0">
                        {{ $minSearchLength - mb_strlen($search) }} more
                    </span>
                @endif
            </label>
        </div>

        <div class="flex items-center gap-2">
            {{-- Filter toggle button (only when WithFilters is active) --}}
            @if(count($filterDefs) > 0)
                <button
                    wire:click="toggleFilters"
                    @class([
                        'btn btn-sm gap-2',
                        'btn-primary' => $filtersOpen || $this->activeFilterCount() > 0,
                        'btn-ghost'   => !$filtersOpen && $this->activeFilterCount() === 0,
                    ])
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                    </svg>
                    Filters
                    @if($this->activeFilterCount() > 0)
                        <span class="badge badge-sm badge-warning">{{ $this->activeFilterCount() }}</span>
                    @endif
                </button>

                @if($this->activeFilterCount() > 0)
                    <button wire:click="resetFilters" class="btn btn-ghost btn-sm" title="Clear all filters">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Clear
                    </button>
                @endif
            @endif

            {{-- Per-page selector --}}
            <div class="flex items-center gap-1.5 text-sm">
                <span class="text-base-content/60 hidden sm:inline">Show</span>
                <select wire:model.live="perPage" class="select select-bordered select-sm">
                    @foreach($this->perPageOptions() as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         FILTER PANEL  (collapsible, rendered when WithFilters is used)
    ═══════════════════════════════════════════════════ --}}
    @if(count($filterDefs) > 0 && $filtersOpen)
        <div class="bg-base-200 rounded-box p-4 mb-3 border border-base-300">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach($filterDefs as $filter)
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-xs font-medium uppercase tracking-wide">
                                {{ $filter['label'] }}
                            </span>
                        </label>

                        @if($filter['type'] === 'text')
                            <input
                                type="text"
                                wire:model.live.debounce.400ms="tableFilters.{{ $filter['column'] }}"
                                placeholder="{{ $filter['placeholder'] ?? 'Filter…' }}"
                                class="input input-bordered input-sm w-full"
                            />

                        @elseif($filter['type'] === 'select')
                            <select
                                wire:model.live="tableFilters.{{ $filter['column'] }}"
                                class="select select-bordered select-sm w-full"
                                @if($filter['multiple']) multiple @endif
                            >
                                <option value="">{{ $filter['placeholder'] ?? 'Any' }}</option>
                                @foreach($filter['options'] as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>

                        @elseif($filter['type'] === 'date')
                            <input
                                type="date"
                                wire:model.live="tableFilters.{{ $filter['column'] }}"
                                class="input input-bordered input-sm w-full"
                            />

                        @elseif($filter['type'] === 'date_range')
                            <div class="flex items-center gap-1">
                                <input
                                    type="date"
                                    wire:model.live="tableFilters.{{ $filter['column'] }}_from"
                                    class="input input-bordered input-sm w-full"
                                    placeholder="From"
                                />
                                <span class="text-base-content/40 text-xs shrink-0">→</span>
                                <input
                                    type="date"
                                    wire:model.live="tableFilters.{{ $filter['toColumn'] ?? $filter['column'] }}_to"
                                    class="input input-bordered input-sm w-full"
                                    placeholder="To"
                                />
                            </div>

                        @elseif($filter['type'] === 'boolean')
                            <select
                                wire:model.live="tableFilters.{{ $filter['column'] }}"
                                class="select select-bordered select-sm w-full"
                            >
                                <option value="">{{ $filter['placeholder'] ?? 'Any' }}</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Active filter chips --}}
    @if($this->activeFilterCount() > 0)
        <div class="flex flex-wrap gap-1.5 mb-3">
            @foreach($filterDefs as $filter)
                @foreach($filter['stateKeys'] as $stateKey)
                    @if(isset($tableFilters[$stateKey]) && $tableFilters[$stateKey] !== '' && $tableFilters[$stateKey] !== null)
                        <span class="badge badge-neutral gap-1">
                            {{ $filter['label'] }}:
                            <strong>{{ is_array($tableFilters[$stateKey]) ? implode(', ', $tableFilters[$stateKey]) : $tableFilters[$stateKey] }}</strong>
                            <button wire:click="resetFilter('{{ $stateKey }}')" class="ml-0.5 hover:text-error transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </span>
                    @endif
                @endforeach
            @endforeach
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════
         TABLE
    ═══════════════════════════════════════════════════ --}}
    <div class="overflow-x-auto rounded-box border border-base-300 relative">

        {{-- Inline loading dimmer (targets everything except global search) --}}
        <div wire:loading wire:target="sort,perPage,resetFilters,resetFilter,updatedTableFilters,toggleFilters"
             class="absolute inset-0 bg-base-100/60 z-10 flex items-center justify-center rounded-box">
            <span class="loading loading-spinner loading-md text-primary"></span>
        </div>

        <table @class([
            'table w-full',
            'table-zebra' => config('tallui.datatable.striped', true),
        ])>
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th class="whitespace-nowrap">
                            @if($column['sortable'] && $column['key'])
                                <button
                                    wire:click="sort('{{ $column['key'] }}')"
                                    class="flex items-center gap-1 hover:text-primary transition-colors font-semibold"
                                >
                                    {{ $column['label'] }}
                                    <span>
                                        @if($sortBy === $column['key'])
                                            @if($sortDirection === 'asc')
                                                <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                            @else
                                                <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            @endif
                                        @else
                                            <svg class="w-3 h-3 opacity-30" viewBox="0 0 20 20" fill="currentColor"><path d="M5 8l5-5 5 5H5zm0 4l5 5 5-5H5z"/></svg>
                                        @endif
                                    </span>
                                </button>
                            @else
                                <span class="font-semibold">{{ $column['label'] }}</span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr class="hover:bg-base-200 transition-colors">
                        @foreach($columns as $column)
                            <td>
                                @if($column['isActions'])
                                    {{-- ── Row actions ───────────────────────── --}}
                                    <div class="flex items-center gap-1">
                                        @foreach($column['actions'] as $action)
                                            @if($action['route'])
                                                <a
                                                    href="{{ route($action['route'], [$action['routeKey'] => data_get($row, $action['routeKey'])]) }}"
                                                    @class(['btn btn-xs', "btn-{$action['color']}"])
                                                    @if($action['confirmMessage'])
                                                        onclick="return confirm('{{ addslashes($action['confirmMessage']) }}')"
                                                    @endif
                                                >
                                                    @if($action['icon'])
                                                        <x-tallui-icon :name="$action['icon']" class="w-3.5 h-3.5" />
                                                    @endif
                                                    <span class="hidden sm:inline">{{ $action['label'] }}</span>
                                                </a>
                                            @elseif($action['emitEvent'])
                                                <button
                                                    wire:click="$dispatch('{{ $action['emitEvent'] }}', { id: {{ data_get($row, $action['emitKey']) }} })"
                                                    @class(['btn btn-xs', "btn-{$action['color']}"])
                                                    @if($action['confirmMessage'])
                                                        wire:confirm="{{ $action['confirmMessage'] }}"
                                                    @endif
                                                >
                                                    @if($action['icon'])
                                                        <x-tallui-icon :name="$action['icon']" class="w-3.5 h-3.5" />
                                                    @endif
                                                    <span class="hidden sm:inline">{{ $action['label'] }}</span>
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>

                                @elseif($column['isHtml'])
                                    {{-- ── Custom HTML (view or renderer class) ─ --}}
                                    {!! $this->renderHtmlColumn($column, $row) !!}

                                @elseif($column['isRaw'])
                                    {{-- ── Trusted raw HTML from model attribute ─ --}}
                                    {!! data_get($row, $column['key'] ?? '') !!}

                                @elseif($column['isBadge'])
                                    {{-- ── Badge with per-value colour mapping ── --}}
                                    @php
                                        $cellValue  = data_get($row, $column['key'] ?? '');
                                        $badgeColor = $column['badgeColors'][(string) $cellValue] ?? ($column['badgeColor'] ?? 'neutral');
                                    @endphp
                                    <span @class(['badge badge-sm', "badge-{$badgeColor}"])>
                                        {{ $cellValue }}
                                    </span>

                                @else
                                    {{-- ── Plain text ───────────────────────── --}}
                                    {{ data_get($row, $column['key'] ?? '') }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="text-center py-12 text-base-content/50">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm">No results found.</span>
                                @if($search !== '' || $this->activeFilterCount() > 0)
                                    <button wire:click="clearSearch" class="btn btn-xs btn-ghost">Clear search & filters</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ═══════════════════════════════════════════════════
         FOOTER  (count · pagination)
    ═══════════════════════════════════════════════════ --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mt-4">
        <div class="text-sm text-base-content/60">
            @if($rows->total() > 0)
                Showing <strong>{{ $rows->firstItem() }}</strong>–<strong>{{ $rows->lastItem() }}</strong>
                of <strong>{{ $rows->total() }}</strong> results
            @else
                No results
            @endif
        </div>
        <div>
            {{ $rows->links() }}
        </div>
    </div>

    {{-- Global loading overlay (full-page requests) --}}
    <div wire:loading wire:target="search,clearSearch"
         class="fixed inset-0 bg-base-100/40 z-50 flex items-center justify-center">
        <span class="loading loading-spinner loading-lg text-primary"></span>
    </div>

</div>
