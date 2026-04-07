<div wire:key="tallui-datatable-{{ $this->getId() }}" class="space-y-4">

    {{-- ══════════════════════════════════════════════════════════
         TOOLBAR
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-wrap items-center justify-between gap-3">

        {{-- Search --}}
        <div class="flex-1 min-w-0 max-w-sm">
            <label class="flex items-center gap-2.5 px-3.5 py-2 rounded-xl border border-base-300 bg-base-100
                          focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20
                          transition-all duration-200 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-base-content/40" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
                <input
                    type="search"
                    wire:model.live.debounce.400ms="search"
                    placeholder="{{ $minSearchLength > 0 ? 'Type ' . $minSearchLength . '+ chars…' : 'Search…' }}"
                    class="flex-1 bg-transparent outline-none border-none text-sm text-base-content placeholder:text-base-content/40 min-w-0"
                    autocomplete="off"
                />
                <span wire:loading wire:target="search" class="loading loading-spinner loading-xs text-primary shrink-0"></span>
                @if($search !== '')
                    <button wire:click="clearSearch"
                        class="shrink-0 w-4 h-4 rounded-full bg-base-content/20 hover:bg-base-content/30 flex items-center justify-center transition-colors"
                        title="Clear">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-base-content/70" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                @endif
                @if($minSearchLength > 0 && $search !== '' && mb_strlen($search) < $minSearchLength)
                    <span class="text-xs font-medium text-warning shrink-0">
                        {{ $minSearchLength - mb_strlen($search) }} more
                    </span>
                @endif
            </label>
        </div>

        <div class="flex items-center gap-2 flex-wrap">

            {{-- Selection indicator --}}
            @if(count($selectedRows) > 0)
                <div class="flex items-center gap-2 pl-3 pr-2 py-1.5 rounded-xl bg-primary/10 border border-primary/20 text-primary text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ count($selectedRows) }} selected</span>
                    <button wire:click="clearSelection"
                        class="ml-0.5 w-4 h-4 rounded-full hover:bg-primary/20 flex items-center justify-center transition-colors"
                        title="Clear selection">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Export CSV --}}
            <button
                wire:click="exportCsv"
                wire:loading.attr="disabled"
                wire:target="exportCsv"
                class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium border border-base-300 bg-base-100
                       text-base-content/70 hover:border-success/50 hover:text-success hover:bg-success/5
                       disabled:opacity-60 transition-all duration-200 shadow-sm"
            >
                <span wire:loading wire:target="exportCsv" class="loading loading-spinner loading-xs"></span>
                <svg wire:loading.remove wire:target="exportCsv" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                @if(count($selectedRows) > 0)
                    Export {{ count($selectedRows) }}
                @else
                    Export all
                @endif
            </button>

            {{-- Filter toggle --}}
            @if(count($filterDefs) > 0)
                <button
                    wire:click="toggleFilters"
                    @class([
                        'flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium border transition-all duration-200',
                        'border-primary bg-primary/10 text-primary shadow-sm'
                            => $filtersOpen || $this->activeFilterCount() > 0,
                        'border-base-300 bg-base-100 text-base-content/70 hover:border-base-400 hover:text-base-content shadow-sm'
                            => !$filtersOpen && $this->activeFilterCount() === 0,
                    ])
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                    </svg>
                    Filters
                    @if($this->activeFilterCount() > 0)
                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-primary text-primary-content text-[10px] font-bold">
                            {{ $this->activeFilterCount() }}
                        </span>
                    @endif
                </button>

                @if($this->activeFilterCount() > 0)
                    <button wire:click="resetFilters"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm border border-base-300 bg-base-100 text-base-content/60 hover:text-error hover:border-error/50 transition-all duration-200 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Clear
                    </button>
                @endif
            @endif

            {{-- Per-page --}}
            <div class="flex items-center gap-2">
                <span class="text-xs text-base-content/50 hidden sm:block">Rows</span>
                <select wire:model.live="perPage"
                    class="select select-bordered select-sm rounded-xl text-sm border-base-300 bg-base-100 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    @foreach($this->perPageOptions() as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════
         FILTER PANEL
    ══════════════════════════════════════════════════════════ --}}
    @if(count($filterDefs) > 0 && $filtersOpen)
        <div class="rounded-2xl border border-base-200 bg-base-50 p-5 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($filterDefs as $filter)
                    <div>
                        <label class="block text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-1.5">
                            {{ $filter['label'] }}
                        </label>

                        @if($filter['type'] === 'text')
                            <input type="text"
                                wire:model.live.debounce.400ms="tableFilters.{{ $filter['column'] }}"
                                placeholder="{{ $filter['placeholder'] ?? 'Filter…' }}"
                                class="input input-sm w-full rounded-xl border-base-300 bg-base-100 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-sm" />

                        @elseif($filter['type'] === 'select')
                            <select wire:model.live="tableFilters.{{ $filter['column'] }}"
                                class="select select-sm w-full rounded-xl border-base-300 bg-base-100 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-sm"
                                @if($filter['multiple']) multiple @endif>
                                <option value="">{{ $filter['placeholder'] ?? 'Any' }}</option>
                                @foreach($filter['options'] as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>

                        @elseif($filter['type'] === 'date')
                            <input type="date"
                                wire:model.live="tableFilters.{{ $filter['column'] }}"
                                class="input input-sm w-full rounded-xl border-base-300 bg-base-100 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-sm" />

                        @elseif($filter['type'] === 'date_range')
                            <div class="flex items-center gap-2">
                                <input type="date"
                                    wire:model.live="tableFilters.{{ $filter['column'] }}_from"
                                    class="input input-sm w-full rounded-xl border-base-300 bg-base-100 focus:border-primary transition-all shadow-sm" />
                                <span class="text-base-content/30 shrink-0">–</span>
                                <input type="date"
                                    wire:model.live="tableFilters.{{ $filter['toColumn'] ?? $filter['column'] }}_to"
                                    class="input input-sm w-full rounded-xl border-base-300 bg-base-100 focus:border-primary transition-all shadow-sm" />
                            </div>

                        @elseif($filter['type'] === 'boolean')
                            <select wire:model.live="tableFilters.{{ $filter['column'] }}"
                                class="select select-sm w-full rounded-xl border-base-300 bg-base-100 focus:border-primary transition-all shadow-sm">
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
        <div class="flex flex-wrap gap-1.5">
            @foreach($filterDefs as $filter)
                @foreach($filter['stateKeys'] as $stateKey)
                    @if(isset($tableFilters[$stateKey]) && $tableFilters[$stateKey] !== '' && $tableFilters[$stateKey] !== null)
                        <span class="inline-flex items-center gap-1.5 pl-2.5 pr-1.5 py-1 rounded-full text-xs font-medium
                                     bg-primary/10 text-primary border border-primary/20">
                            <span class="opacity-70">{{ $filter['label'] }}:</span>
                            <strong>{{ is_array($tableFilters[$stateKey]) ? implode(', ', $tableFilters[$stateKey]) : $tableFilters[$stateKey] }}</strong>
                            <button wire:click="resetFilter('{{ $stateKey }}')"
                                class="w-3.5 h-3.5 rounded-full hover:bg-primary/20 flex items-center justify-center transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </span>
                    @endif
                @endforeach
            @endforeach
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         TABLE
    ══════════════════════════════════════════════════════════ --}}
    @php
        $bp = $mobileBreakpoint ?: '';
        $tableClass = $bp ? "hidden {$bp}:block" : 'block';
        $cardsClass  = $bp ? "{$bp}:hidden" : 'hidden';
        // Columns that carry data (not actions), for the mobile card stack
        $dataColumns    = array_filter($columns, fn($c) => !$c['isActions'] && $c['key']);
        $actionColumns  = array_filter($columns, fn($c) => $c['isActions']);
        $primaryColumn  = !empty($dataColumns) ? array_values($dataColumns)[0] : null;
        $secondaryColumns = $primaryColumn ? array_slice(array_values($dataColumns), 1) : [];
    @endphp

    <div class="relative rounded-2xl border border-base-200 overflow-hidden shadow-sm bg-base-100">

        {{-- Loading overlay --}}
        <div wire:loading wire:target="sort,perPage,resetFilters,resetFilter,updatedTableFilters,toggleFilters,togglePageSelection"
             class="absolute inset-0 bg-base-100/70 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-2xl">
            <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-base-100 shadow-md border border-base-200">
                <span class="loading loading-spinner loading-sm text-primary"></span>
                <span class="text-sm text-base-content/60">Loading…</span>
            </div>
        </div>

        {{-- ── Mobile card stack ─────────────────────────────────────────── --}}
        @if($bp)
        <div class="{{ $cardsClass }} divide-y divide-base-200">
            @forelse($rows as $row)
                @php $rowId = (string) data_get($row, $primaryKey) @endphp
                <div @class([
                        'flex items-start gap-3 px-4 py-3',
                        'bg-primary/5' => in_array($rowId, $selectedRows),
                    ])>
                    {{-- Checkbox --}}
                    <div class="pt-0.5 shrink-0">
                        <input
                            type="checkbox"
                            class="checkbox checkbox-sm checkbox-primary rounded"
                            wire:click="toggleRow('{{ $rowId }}')"
                            @if(in_array($rowId, $selectedRows)) checked @endif
                        />
                    </div>

                    {{-- Card body --}}
                    <div class="flex-1 min-w-0">
                        {{-- Primary field --}}
                        @if($primaryColumn)
                            <div class="font-semibold text-sm text-base-content truncate">
                                @if($primaryColumn['isBadge'])
                                    @php
                                        $cv = data_get($row, $primaryColumn['key']);
                                        $bc = $primaryColumn['badgeColors'][(string)$cv] ?? ($primaryColumn['badgeColor'] ?? 'neutral');
                                    @endphp
                                    <span class="badge badge-sm badge-{{ $bc }}">{{ $cv }}</span>
                                @elseif($primaryColumn['isRaw'])
                                    {!! data_get($row, $primaryColumn['key']) !!}
                                @elseif($primaryColumn['isHtml'])
                                    {!! $this->renderHtmlColumn($primaryColumn, $row) !!}
                                @else
                                    {{ data_get($row, $primaryColumn['key']) }}
                                @endif
                            </div>
                        @endif

                        {{-- Secondary fields --}}
                        @if(!empty($secondaryColumns))
                            <dl class="mt-1 grid grid-cols-2 gap-x-4 gap-y-0.5">
                                @foreach($secondaryColumns as $col)
                                    <div class="flex gap-1 items-baseline min-w-0">
                                        <dt class="text-xs text-base-content/40 shrink-0">{{ $col['label'] }}:</dt>
                                        <dd class="text-xs text-base-content/70 truncate">
                                            @if($col['isBadge'])
                                                @php
                                                    $cv = data_get($row, $col['key']);
                                                    $bc = $col['badgeColors'][(string)$cv] ?? ($col['badgeColor'] ?? 'neutral');
                                                @endphp
                                                <span class="badge badge-xs badge-{{ $bc }}">{{ $cv }}</span>
                                            @elseif($col['isRaw'])
                                                {!! data_get($row, $col['key']) !!}
                                            @elseif($col['isHtml'])
                                                {!! $this->renderHtmlColumn($col, $row) !!}
                                            @else
                                                {{ data_get($row, $col['key']) ?? '—' }}
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        @endif
                    </div>

                    {{-- Actions --}}
                    @foreach($actionColumns as $actionCol)
                        <div class="flex items-center gap-1 shrink-0">
                            @foreach($actionCol['actions'] as $action)
                                @if($action['route'])
                                    <a href="{{ route($action['route'], [$action['routeKey'] => data_get($row, $action['routeKey'])]) }}"
                                        @class([
                                            'inline-flex items-center gap-1 p-1.5 rounded-lg text-xs font-medium border transition-all duration-150',
                                            "bg-{$action['color']}/10 text-{$action['color']} border-{$action['color']}/20 hover:bg-{$action['color']}/20",
                                        ])
                                        @if($action['confirmMessage'])
                                            onclick="return confirm('{{ addslashes($action['confirmMessage']) }}')"
                                        @endif
                                        title="{{ $action['label'] }}">
                                        @if($action['icon'])
                                            <x-tallui-icon :name="$action['icon']" class="w-4 h-4" />
                                        @else
                                            {{ $action['label'] }}
                                        @endif
                                    </a>
                                @elseif($action['emitEvent'])
                                    <button
                                        wire:click="$dispatch('{{ $action['emitEvent'] }}', { id: {{ data_get($row, $action['emitKey']) }} })"
                                        @class([
                                            'inline-flex items-center gap-1 p-1.5 rounded-lg text-xs font-medium border transition-all duration-150',
                                            "bg-{$action['color']}/10 text-{$action['color']} border-{$action['color']}/20 hover:bg-{$action['color']}/20",
                                        ])
                                        @if($action['confirmMessage'])
                                            wire:confirm="{{ $action['confirmMessage'] }}"
                                        @endif
                                        title="{{ $action['label'] }}">
                                        @if($action['icon'])
                                            <x-tallui-icon :name="$action['icon']" class="w-4 h-4" />
                                        @else
                                            {{ $action['label'] }}
                                        @endif
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="py-16 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-base-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-base-content/70">No results found</p>
                            @if($search !== '' || $this->activeFilterCount() > 0)
                                <p class="text-sm text-base-content/40 mt-0.5">Try adjusting your search or filters</p>
                            @endif
                        </div>
                        @if($search !== '' || $this->activeFilterCount() > 0)
                            <button wire:click="clearSearch" class="text-sm text-primary hover:underline font-medium">Clear all</button>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
        @endif

        {{-- ── Desktop table ─────────────────────────────────────────────── --}}
        <div class="{{ $tableClass }} overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-base-200 bg-base-50">
                        {{-- Select-all checkbox --}}
                        <th class="pl-5 pr-2 py-3 w-10"
                            x-data
                            x-init="$nextTick(() => { $refs.selAll.indeterminate = {{ $pagePartiallySelected ? 'true' : 'false' }} })">
                            <input
                                type="checkbox"
                                x-ref="selAll"
                                class="checkbox checkbox-sm checkbox-primary rounded"
                                wire:click="togglePageSelection"
                                @if($pageFullySelected) checked @endif
                            />
                        </th>
                        @foreach($columns as $column)
                            @php
                                $thClass = 'px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider text-base-content/50 whitespace-nowrap first:pl-5 last:pr-5';
                                if (!empty($column['visibleFrom'])) {
                                    $thClass .= " hidden {$column['visibleFrom']}:table-cell";
                                }
                            @endphp
                            <th class="{{ $thClass }}">
                                @if($column['sortable'] && $column['key'])
                                    <button
                                        wire:click="sort('{{ $column['key'] }}')"
                                        class="group inline-flex items-center gap-1.5 hover:text-primary transition-colors duration-150"
                                    >
                                        {{ $column['label'] }}
                                        <span class="flex flex-col gap-px">
                                            @if($sortBy === $column['key'])
                                                @if($sortDirection === 'asc')
                                                    <svg class="w-3 h-3 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            @else
                                                <svg class="w-3 h-3 opacity-20 group-hover:opacity-50 transition-opacity" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M5 8l5-5 5 5H5zm0 4l5 5 5-5H5z"/>
                                                </svg>
                                            @endif
                                        </span>
                                    </button>
                                @else
                                    {{ $column['label'] }}
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200">
                    @forelse($rows as $row)
                        @php $rowId = (string) data_get($row, $primaryKey) @endphp
                        <tr @class([
                                'group transition-colors duration-100',
                                'bg-primary/5 hover:bg-primary/8' => in_array($rowId, $selectedRows),
                                'hover:bg-base-50' => !in_array($rowId, $selectedRows),
                            ])>
                            {{-- Row checkbox --}}
                            <td class="pl-5 pr-2 py-3.5 w-10">
                                <input
                                    type="checkbox"
                                    class="checkbox checkbox-sm checkbox-primary rounded"
                                    wire:click="toggleRow('{{ $rowId }}')"
                                    @if(in_array($rowId, $selectedRows)) checked @endif
                                />
                            </td>
                            @foreach($columns as $column)
                                @php
                                    $tdClass = 'px-4 py-3.5 text-base-content first:pl-5 last:pr-5 whitespace-nowrap';
                                    if (!empty($column['visibleFrom'])) {
                                        $tdClass .= " hidden {$column['visibleFrom']}:table-cell";
                                    }
                                @endphp
                                <td class="{{ $tdClass }}">
                                    @if($column['isActions'])
                                        <div class="flex items-center gap-1.5">
                                            @foreach($column['actions'] as $action)
                                                @if($action['route'])
                                                    <a href="{{ route($action['route'], [$action['routeKey'] => data_get($row, $action['routeKey'])]) }}"
                                                        @class([
                                                            'inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border transition-all duration-150',
                                                            "bg-{$action['color']}/10 text-{$action['color']} border-{$action['color']}/20 hover:bg-{$action['color']}/20",
                                                        ])
                                                        @if($action['confirmMessage'])
                                                            onclick="return confirm('{{ addslashes($action['confirmMessage']) }}')"
                                                        @endif>
                                                        @if($action['icon'])
                                                            <x-tallui-icon :name="$action['icon']" class="w-3.5 h-3.5" />
                                                        @endif
                                                        <span class="hidden sm:inline">{{ $action['label'] }}</span>
                                                    </a>
                                                @elseif($action['emitEvent'])
                                                    <button
                                                        wire:click="$dispatch('{{ $action['emitEvent'] }}', { id: {{ data_get($row, $action['emitKey']) }} })"
                                                        @class([
                                                            'inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border transition-all duration-150',
                                                            "bg-{$action['color']}/10 text-{$action['color']} border-{$action['color']}/20 hover:bg-{$action['color']}/20",
                                                        ])
                                                        @if($action['confirmMessage'])
                                                            wire:confirm="{{ $action['confirmMessage'] }}"
                                                        @endif>
                                                        @if($action['icon'])
                                                            <x-tallui-icon :name="$action['icon']" class="w-3.5 h-3.5" />
                                                        @endif
                                                        <span class="hidden sm:inline">{{ $action['label'] }}</span>
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>

                                    @elseif($column['isHtml'])
                                        {!! $this->renderHtmlColumn($column, $row) !!}

                                    @elseif($column['isRaw'])
                                        {!! data_get($row, $column['key'] ?? '') !!}

                                    @elseif($column['isBadge'])
                                        @php
                                            $cellValue  = data_get($row, $column['key'] ?? '');
                                            $badgeColor = $column['badgeColors'][(string) $cellValue] ?? ($column['badgeColor'] ?? 'neutral');
                                        @endphp
                                        <span @class([
                                            'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                            "badge badge-sm badge-{$badgeColor}",
                                        ])>{{ $cellValue }}</span>

                                    @else
                                        <span class="text-base-content/80">{{ data_get($row, $column['key'] ?? '') }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-base-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-base-content/70">No results found</p>
                                        @if($search !== '' || $this->activeFilterCount() > 0)
                                            <p class="text-sm text-base-content/40 mt-0.5">Try adjusting your search or filters</p>
                                        @endif
                                    </div>
                                    @if($search !== '' || $this->activeFilterCount() > 0)
                                        <button wire:click="clearSearch"
                                            class="text-sm text-primary hover:underline font-medium">
                                            Clear all
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-xs text-base-content/40">
            @if($rows->total() > 0)
                Showing
                <span class="font-semibold text-base-content/70">{{ $rows->firstItem() }}–{{ $rows->lastItem() }}</span>
                of
                <span class="font-semibold text-base-content/70">{{ $rows->total() }}</span>
                results
            @else
                No results
            @endif
        </p>
        <div>{{ $rows->links() }}</div>
    </div>

    {{-- Global search overlay --}}
    <div wire:loading wire:target="search,clearSearch"
         class="fixed inset-0 bg-base-100/40 backdrop-blur-[2px] z-50 flex items-center justify-center">
        <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-base-100 shadow-xl border border-base-200">
            <span class="loading loading-spinner loading-sm text-primary"></span>
            <span class="text-sm font-medium text-base-content/70">Searching…</span>
        </div>
    </div>
</div>
