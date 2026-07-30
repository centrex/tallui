<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Loading placeholder for a lazy-loaded card-style Livewire component (see
 * `Livewire\Component::placeholder()`), sized to match `<x-tallui-card>` so the real
 * content doesn't shift the layout when it swaps in.
 */
class CardSkeleton extends Component
{
    public function __construct(
        public ?string $title = null,
        public string $variant = 'stats', // stats | chart
        public int $rows = 3,
        public string $height = 'h-64',
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div role="status" aria-label="Loading {{ $title ?? 'card' }}">
                <x-tallui-card :title="$title" :shadow="true">
                    @if($variant === 'chart')
                        <x-tallui-skeleton variant="rect" width="w-full" :height="$height" />
                    @else
                        <div class="space-y-3">
                            @for ($i = 0; $i < $rows; $i++)
                                <div class="flex items-center justify-between gap-3">
                                    <x-tallui-skeleton width="w-24" height="h-3" />
                                    <x-tallui-skeleton width="w-16" height="h-4" />
                                </div>
                            @endfor
                        </div>
                    @endif
                </x-tallui-card>
            </div>
            BLADE;
    }
}
