<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Rating extends Component
{
    public function __construct(
        public string $name = 'rating',
        public int $max = 5,
        public int|float $value = 0,
        public string $size = 'md',
        public string $color = 'warning',
        public bool $readonly = false,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class(['rating', "rating-{$size}", 'rating-half' => false]) {{ $attributes }}>
                <input type="radio" name="{{ $name }}" class="rating-hidden" value="0"
                    @checked($value == 0) @if($readonly) disabled @endif />

                @for($i = 1; $i <= $max; $i++)
                    <input
                        type="radio"
                        name="{{ $name }}"
                        value="{{ $i }}"
                        @class(['mask mask-star-2', "bg-{$color}"])
                        @checked($value == $i)
                        @if($readonly) disabled @endif
                    />
                @endfor
            </div>
            BLADE;
    }
}
