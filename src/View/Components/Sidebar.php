<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public function __construct(
        public string $id = 'sidebar',
        public string $position = 'left',    // left | right
        public string $width = 'w-64',
        public bool $overlay = true,
        public bool $persistent = false,     // always visible on lg+
        public string $collapseBreakpoint = 'lg',  // breakpoint at which sidebar shows always
        public ?string $header = null,
        public ?string $footer = null,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    open: {{ $persistent ? 'window.innerWidth >= 1024' : 'false' }},
                    toggle() { this.open = !this.open; },
                    close() { this.open = false; },
                }"
                @open-sidebar.window="if ($event.detail === '{{ $id }}') open = true"
                @close-sidebar.window="if ($event.detail === '{{ $id }}') open = false"
                @toggle-sidebar.window="if ($event.detail === '{{ $id }}') toggle()"
                @keydown.escape.window="if (!{{ $persistent ? 'true' : 'false' }}) close()"
                class="relative"
                {{ $attributes }}
            >
                {{-- Overlay --}}
                @if($overlay)
                    <div
                        x-show="open"
                        x-transition:enter="transition-opacity ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @click="close()"
                        class="{{ $persistent ? $collapseBreakpoint . ':hidden' : '' }} fixed inset-0 z-[998] bg-black/40"
                        style="display:none"
                    ></div>
                @endif

                {{-- Sidebar panel --}}
                <aside
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200 transform"
                    x-transition:enter-start="{{ $position === 'right' ? 'translate-x-full' : '-translate-x-full' }} opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transition ease-in duration-150 transform"
                    x-transition:leave-start="translate-x-0 opacity-100"
                    x-transition:leave-end="{{ $position === 'right' ? 'translate-x-full' : '-translate-x-full' }} opacity-0"
                    @class([
                        'fixed top-0 bottom-0 z-[999] flex flex-col bg-base-100 shadow-2xl overflow-hidden',
                        $width,
                        'left-0'  => $position === 'left',
                        'right-0' => $position === 'right',
                        $collapseBreakpoint . ':relative ' . $collapseBreakpoint . ':translate-x-0 ' . $collapseBreakpoint . ':z-auto ' . $collapseBreakpoint . ':shadow-none' => $persistent,
                    ])
                    style="display:none"
                >
                    {{-- Sidebar header --}}
                    @if($header || isset($headerSlot))
                        <div class="flex items-center justify-between px-4 py-4 border-b border-base-200 shrink-0">
                            @if(isset($headerSlot))
                                {{ $headerSlot }}
                            @elseif($header)
                                <span class="font-semibold text-lg">{{ $header }}</span>
                            @endif
                            @if(!$persistent)
                                <button
                                    @click="close()"
                                    class="{{ $persistent ? $collapseBreakpoint . ':hidden' : '' }} btn btn-ghost btn-sm btn-circle ml-auto"
                                    aria-label="Close sidebar"
                                >
                                    <x-tallui-icon name="o-x-mark" class="w-4 h-4" />
                                </button>
                            @endif
                        </div>
                    @endif

                    {{-- Sidebar body --}}
                    <div class="flex-1 overflow-y-auto py-3">
                        {{ $slot }}
                    </div>

                    {{-- Sidebar footer --}}
                    @if(isset($footerSlot))
                        <div class="shrink-0 border-t border-base-200 px-4 py-3">
                            {{ $footerSlot }}
                        </div>
                    @endif
                </aside>
            </div>
            BLADE;
    }
}
