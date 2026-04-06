<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Stat extends Component
{
    public function __construct(
        public string $title = '',
        public string $value = '',
        public ?string $desc = null,
        public ?string $icon = null,
        public string $iconColor = 'text-primary',
        public ?string $change = null,
        public string $changeType = 'neutral', // up | down | neutral
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div class="stat" {{ $attributes }}>
                @if($icon)
                    <div class="stat-figure {{ $iconColor }}">
                        <x-tallui-icon :name="$icon" class="w-8 h-8" />
                    </div>
                @elseif($slot->isNotEmpty())
                    <div class="stat-figure">{{ $slot }}</div>
                @endif

                <div class="stat-title">{{ $title }}</div>
                <div class="stat-value">{{ $value }}</div>

                @if($desc || $change)
                    <div class="stat-desc flex items-center gap-1">
                        @if($change)
                            @if($changeType === 'up')
                                <svg class="w-3 h-3 text-success" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-success">{{ $change }}</span>
                            @elseif($changeType === 'down')
                                <svg class="w-3 h-3 text-error" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-error">{{ $change }}</span>
                            @else
                                <span>{{ $change }}</span>
                            @endif
                        @endif
                        @if($desc)
                            <span>{{ $desc }}</span>
                        @endif
                    </div>
                @endif
            </div>
            BLADE;
    }
}
