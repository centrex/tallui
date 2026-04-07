<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageHeader extends Component
{
    public function __construct(
        public string $title = '',
        public ?string $subtitle = null,
        public ?string $icon = null,
        public bool $separator = true,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div class="mb-6" {{ $attributes }}>
                @if(isset($breadcrumbs))
                    <div class="mb-2 text-sm">{{ $breadcrumbs }}</div>
                @endif

                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        @if($icon)
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                <span class="text-primary">
                                    <x-tallui-icon :name="$icon" class="w-5 h-5" />
                                </span>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-2xl font-bold text-base-content">{{ $title }}</h1>
                            @if($subtitle)
                                <p class="text-sm text-base-content/50 mt-0.5">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>

                    @if(isset($actions))
                        <div class="flex items-center gap-2 flex-wrap">
                            {{ $actions }}
                        </div>
                    @endif
                </div>

                @if($separator)
                    <div class="mt-4 border-b border-base-200"></div>
                @endif
            </div>
            BLADE;
    }
}
