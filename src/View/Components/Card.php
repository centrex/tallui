<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?string $icon = null,
        public bool $shadow = true,
        public bool $bordered = false,
        public string $padding = 'normal',   // none | compact | normal | loose
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class([
                'card bg-base-100',
                'shadow-md'    => $shadow,
                'card-bordered' => $bordered,
                'card-compact' => $padding === 'compact',
                'card-normal'  => $padding === 'normal',
            ]) {{ $attributes }}>
                @if($title || isset($actions))
                    <div class="card-title-area flex items-center justify-between px-6 pt-5 pb-0">
                        <div class="flex items-center gap-3">
                            @if($icon)
                                <span class="text-primary">
                                    <x-tallui-icon :name="$icon" class="w-5 h-5" />
                                </span>
                            @endif
                            <div>
                                @if($title)
                                    <h3 class="font-semibold text-base-content">{{ $title }}</h3>
                                @endif
                                @if($subtitle)
                                    <p class="text-sm text-base-content/50">{{ $subtitle }}</p>
                                @endif
                            </div>
                        </div>
                        @if(isset($actions))
                            <div class="flex items-center gap-2">{{ $actions }}</div>
                        @endif
                    </div>
                @endif

                <div @class([
                    'card-body',
                    'pt-4' => $title || isset($actions),
                    'p-0'  => $padding === 'none',
                ])>
                    {{ $slot }}
                </div>

                @if(isset($footer))
                    <div class="card-footer border-t border-base-200 px-6 py-3">
                        {{ $footer }}
                    </div>
                @endif
            </div>
            BLADE;
    }
}
