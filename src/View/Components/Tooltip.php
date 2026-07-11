<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tooltip extends Component
{
    public function __construct(
        public string $text = '',
        public string $position = 'top',   // top | bottom | left | right
        public string $color = '',         // '' | primary | secondary | accent | neutral | success | warning | error | info
        public bool $open = false,         // force-show, useful for onboarding tours
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                @class([
                    'tooltip',
                    "tooltip-{$position}" => $position,
                    "tooltip-{$color}"    => $color,
                    'tooltip-open'        => $open,
                ])
                data-tip="{{ $text }}"
                {{ $attributes }}
            >{{ $slot }}</div>
            BLADE;
    }
}
