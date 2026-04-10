<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    public function __construct(
        public ?string $brand = null,
        public ?string $brandHref = '/',
        public ?string $brandLogo = null,
        public bool $sticky = true,
        public bool $shadow = true,
        public string $height = 'h-16',
        public string $sidebarId = '',     // if set, renders hamburger that toggles this sidebar
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <header @class([
                'z-40 bg-base-100 border-b border-base-200 w-full',
                'sticky top-0' => $sticky,
                'shadow-sm'    => $shadow,
            ])>
                <div class="navbar {{ $height }} px-4 gap-3">

                    {{-- Hamburger (sidebar toggle) --}}
                    @if($sidebarId)
                        <button
                            type="button"
                            @click="$dispatch('toggle-sidebar', '{{ $sidebarId }}')"
                            class="btn btn-ghost btn-sm btn-circle"
                            aria-label="Toggle sidebar"
                        >
                            <x-tallui-icon name="o-bars-3" class="w-5 h-5" />
                        </button>
                    @endif

                    {{-- Brand --}}
                    <div class="navbar-start gap-3">
                        @if(isset($brandSlot))
                            {{ $brandSlot }}
                        @elseif($brand || $brandLogo)
                            <a href="{{ $brandHref }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                                @if($brandLogo)
                                    <img src="{{ $brandLogo }}" alt="{{ $brand }}" class="h-8 w-auto" />
                                @endif
                                @if($brand)
                                    <span class="font-bold text-lg">{{ $brand }}</span>
                                @endif
                            </a>
                        @endif
                    </div>

                    {{-- Center (nav links etc.) --}}
                    @if(isset($center))
                        <div class="navbar-center hidden md:flex">
                            {{ $center }}
                        </div>
                    @endif

                    {{-- Right actions --}}
                    <div class="navbar-end gap-2 ml-auto">
                        @if(isset($actions))
                            {{ $actions }}
                        @endif

                        {{-- Default slot (full custom content) --}}
                        @if(!isset($actions) && !isset($center) && $slot->isNotEmpty())
                            {{ $slot }}
                        @endif
                    </div>
                </div>
            </header>
            BLADE;
    }
}
