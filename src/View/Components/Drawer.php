<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Drawer extends Component
{
    public function __construct(
        public string $id = 'drawer',
        public string $side = 'left',  // left | right
        public string $width = 'w-80',
        public bool $open = false,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    open: {{ $open ? 'true' : 'false' }},
                    returnFocusEl: null,
                }"
                x-effect="if (open) { returnFocusEl = returnFocusEl ?? document.activeElement; $nextTick(() => $refs.panel?.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex=\'-1\'])')?.focus()); } else if (returnFocusEl) { returnFocusEl.focus?.(); returnFocusEl = null; }"
                @keydown.escape.window="open = false"
                class="drawer {{ $side === 'right' ? 'drawer-end' : '' }}"
                {{ $attributes }}
            >
                <input id="{{ $id }}" type="checkbox" class="drawer-toggle" x-model="open" />

                {{-- Page content --}}
                <div class="drawer-content">
                    {{ $slot }}
                </div>

                {{-- Sidebar --}}
                <div class="drawer-side z-50">
                    <label for="{{ $id }}" @click="open = false" aria-label="Close menu" class="drawer-overlay"></label>
                    <div x-ref="panel" role="dialog" :aria-modal="open" class="{{ $width }} min-h-full bg-base-100 border-r border-base-200">
                        {{ $sidebar ?? '' }}
                    </div>
                </div>
            </div>
            BLADE;
    }
}
