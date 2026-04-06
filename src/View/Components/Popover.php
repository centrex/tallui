<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Popover extends Component
{
    public function __construct(
        public string $position = 'top',   // top | bottom | left | right
        public string $trigger = 'hover', // hover | click
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{ open: false }"
                @if($trigger === 'hover')
                    @mouseenter="open = true"
                    @mouseleave="open = false"
                @else
                    @click.stop="open = !open"
                    @click.outside="open = false"
                @endif
                class="relative inline-block"
                {{ $attributes }}
            >
                {{-- Trigger slot --}}
                <div>{{ $trigger_slot ?? $slot }}</div>

                {{-- Popover content --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @class([
                        'absolute z-50 bg-base-100 border border-base-300 rounded-box shadow-lg p-3 text-sm min-w-max',
                        'bottom-full mb-2 left-1/2 -translate-x-1/2' => $position === 'top',
                        'top-full mt-2 left-1/2 -translate-x-1/2'    => $position === 'bottom',
                        'right-full mr-2 top-1/2 -translate-y-1/2'   => $position === 'left',
                        'left-full ml-2 top-1/2 -translate-y-1/2'    => $position === 'right',
                    ])
                    style="display:none"
                >
                    {{ $content ?? '' }}
                </div>
            </div>
            BLADE;
    }
}
