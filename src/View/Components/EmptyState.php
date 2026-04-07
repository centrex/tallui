<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EmptyState extends Component
{
    public function __construct(
        public string $title = 'No results found',
        public ?string $description = null,
        public string $icon = 'heroicon-o-inbox',
        public string $size = 'md',   // sm | md | lg
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class([
                'flex flex-col items-center justify-center text-center',
                'py-8 px-4'  => $size === 'sm',
                'py-14 px-6' => $size === 'md',
                'py-20 px-8' => $size === 'lg',
            ]) {{ $attributes }}>
                <div @class([
                    'rounded-2xl bg-base-200 flex items-center justify-center mb-4',
                    'w-12 h-12' => $size === 'sm',
                    'w-16 h-16' => $size === 'md',
                    'w-20 h-20' => $size === 'lg',
                ])>
                    <x-tallui-icon :name="$icon" @class([
                        'text-base-content/30',
                        'w-6 h-6'  => $size === 'sm',
                        'w-8 h-8'  => $size === 'md',
                        'w-10 h-10' => $size === 'lg',
                    ]) />
                </div>
                <h3 @class([
                    'font-semibold text-base-content/70',
                    'text-sm' => $size === 'sm',
                    'text-base' => $size === 'md',
                    'text-lg' => $size === 'lg',
                ])>{{ $title }}</h3>
                @if($description)
                    <p class="mt-1 text-sm text-base-content/40">{{ $description }}</p>
                @endif
                @if($slot->isNotEmpty())
                    <div class="mt-4">{{ $slot }}</div>
                @endif
            </div>
            BLADE;
    }
}
