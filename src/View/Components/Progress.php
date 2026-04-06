<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Progress extends Component
{
    public int $percent;

    public function __construct(
        public int|float $value = 0,
        public int|float $max = 100,
        public string $color = 'primary',
        public string $size = 'md',   // xs | sm | md | lg
        public bool $showLabel = false,
        public ?string $label = null,
    ) {
        $this->percent = $max > 0 ? (int) round(($value / $max) * 100) : 0;
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div class="w-full" {{ $attributes }}>
                @if($label || $showLabel)
                    <div class="flex justify-between items-center mb-1 text-sm">
                        <span class="text-base-content/70">{{ $label }}</span>
                        @if($showLabel)
                            <span class="font-medium">{{ $percent }}%</span>
                        @endif
                    </div>
                @endif
                <progress
                    @class(['progress', "progress-{$color}", "progress-{$size}"])
                    value="{{ $value }}"
                    max="{{ $max }}"
                ></progress>
            </div>
            BLADE;
    }
}
