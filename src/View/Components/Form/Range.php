<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Range extends Component
{
    public function __construct(
        public string $name = '',
        public ?string $label = null,
        public int $min = 0,
        public int $max = 100,
        public int $step = 1,
        public ?int $value = null,
        public string $color = 'primary',
        public bool $showSteps = false,
        public bool $showValue = false,
        public ?string $helper = null,
        public ?string $error = null,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div class="form-control w-full" x-data="{ val: {{ $value ?? $min }} }">
                @if($label || $showValue)
                    <label @if($name) for="{{ $name }}" @endif class="label">
                        @if($label)
                            <span class="label-text font-medium">{{ $label }}</span>
                        @endif
                        @if($showValue)
                            <span class="label-text-alt font-mono" x-text="val"></span>
                        @endif
                    </label>
                @endif

                <input
                    type="range"
                    id="{{ $name }}"
                    name="{{ $name }}"
                    min="{{ $min }}"
                    max="{{ $max }}"
                    step="{{ $step }}"
                    x-model="val"
                    {{ $attributes->class([
                        'range',
                        "range-{$color}",
                        'range-error' => $error,
                    ]) }}
                />

                @if($showSteps)
                    <div class="flex justify-between text-xs text-base-content/50 px-1 mt-1">
                        @for($i = $min; $i <= $max; $i += $step * max(1, intdiv($max - $min, 5)))
                            <span>{{ $i }}</span>
                        @endfor
                    </div>
                @endif

                @if($error)
                    <label class="label"><span class="label-text-alt text-error">{{ $error }}</span></label>
                @elseif($helper)
                    <label class="label"><span class="label-text-alt text-base-content/60">{{ $helper }}</span></label>
                @endif
            </div>
            BLADE;
    }
}
