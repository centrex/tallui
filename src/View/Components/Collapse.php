<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Centrex\TallUi\Concerns\HasUuid;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Collapse extends Component
{
    use HasUuid;

    public function __construct(
        public string $title = '',
        public bool $open = false,
        public bool $bordered = false,
        public string $variant = 'arrow',   // arrow | plus | none
        public string $titleClass = '',
        public string $contentClass = '',
    ) {
        $this->generateUuid();
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{ open: {{ $open ? 'true' : 'false' }} }"
                @class([
                    'collapse w-full',
                    "collapse-{$variant}" => $variant !== 'none',
                    'collapse-open'  => $open,
                    'border border-base-300 rounded-box' => $bordered,
                    'bg-base-200 rounded-box' => !$bordered,
                ])
                {{ $attributes }}
            >
                <div
                    id="{{ $uuid }}-title"
                    class="collapse-title font-semibold cursor-pointer select-none {{ $titleClass }}"
                    role="button"
                    tabindex="0"
                    :aria-expanded="open ? 'true' : 'false'"
                    aria-controls="{{ $uuid }}-content"
                    @click="open = !open"
                    @keydown.enter.prevent="open = !open"
                    @keydown.space.prevent="open = !open"
                >
                    {{ $title }}
                    @if(isset($titleSlot))
                        {{ $titleSlot }}
                    @endif
                </div>

                <div
                    id="{{ $uuid }}-content"
                    role="region"
                    aria-labelledby="{{ $uuid }}-title"
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="collapse-content text-sm {{ $contentClass }}"
                    style="display:none"
                >
                    {{ $slot }}
                </div>
            </div>
            BLADE;
    }
}
