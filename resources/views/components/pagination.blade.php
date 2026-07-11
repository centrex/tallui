@if($lastPage > 1)
<nav aria-label="Pagination" @class([
    'flex flex-wrap items-center gap-3',
    'justify-start'  => $align === 'start',
    'justify-center' => $align === 'center',
    'justify-end'    => $align === 'end',
]) {{ $attributes }}>

    {{-- Info text --}}
    @if($showInfo && $total > 0)
        <span class="text-sm text-base-content/50 order-first sm:order-none">
            Showing <span class="font-medium text-base-content">{{ $firstItem }}</span>–<span class="font-medium text-base-content">{{ $lastItem }}</span>
            of <span class="font-medium text-base-content">{{ number_format($total) }}</span>
        </span>
    @endif

    {{-- Page buttons — size is applied per-button via btn-{size}; `join` has no size modifier --}}
    <div class="join">

        {{-- Prev --}}
        @if($pg->onFirstPage())
            <button type="button" class="join-item btn btn-{{ $size ?: 'sm' }} btn-disabled" disabled aria-label="Previous page">
                <x-tallui-icon name="o-chevron-left" class="w-4 h-4" />
            </button>
        @else
            <button
                type="button"
                wire:click="previousPage"
                class="join-item btn btn-{{ $size ?: 'sm' }}"
                aria-label="Previous page"
            >
                <x-tallui-icon name="o-chevron-left" class="w-4 h-4" />
            </button>
        @endif

        {{-- Window --}}
        @foreach($window as $page)
            @if($page === $gap)
                <button type="button" class="join-item btn btn-{{ $size ?: 'sm' }} btn-disabled pointer-events-none" disabled>…</button>
            @elseif($page === $current)
                <button type="button" class="join-item btn btn-{{ $size ?: 'sm' }} btn-active" aria-current="page" disabled>{{ $page }}</button>
            @else
                <button
                    type="button"
                    wire:click="gotoPage({{ $page }})"
                    class="join-item btn btn-{{ $size ?: 'sm' }}"
                    aria-label="Page {{ $page }}"
                >{{ $page }}</button>
            @endif
        @endforeach

        {{-- Next --}}
        @if($pg->hasMorePages())
            <button
                type="button"
                wire:click="nextPage"
                class="join-item btn btn-{{ $size ?: 'sm' }}"
                aria-label="Next page"
            >
                <x-tallui-icon name="o-chevron-right" class="w-4 h-4" />
            </button>
        @else
            <button type="button" class="join-item btn btn-{{ $size ?: 'sm' }} btn-disabled" disabled aria-label="Next page">
                <x-tallui-icon name="o-chevron-right" class="w-4 h-4" />
            </button>
        @endif
    </div>

    {{-- Per-page selector --}}
    @if($showPerPage)
        <select
            class="select select-bordered select-{{ $size ?: 'sm' }} w-auto"
            onchange="window.location.href = '{{ $pg->url(1) }}&per_page=' + this.value"
        >
            @foreach($perPageOptions as $opt)
                <option value="{{ $opt }}" @selected($opt === $pg->perPage())>{{ $opt }} / page</option>
            @endforeach
        </select>
    @endif

</nav>
@endif
