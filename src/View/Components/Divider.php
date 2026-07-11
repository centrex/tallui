<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Divider extends Component
{
    public function __construct(
        public ?string $label = null,
        public bool $vertical = false,
        public string $color = '',   // '' | primary | secondary | accent | neutral | success | warning | error | info
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                role="separator"
                @if($label) aria-label="{{ $label }}" @else aria-hidden="true" @endif
                @class([
                    'divider',
                    'divider-horizontal' => $vertical,
                    "divider-{$color}"   => $color,
                ])
                {{ $attributes }}
            >{{ $label ?? $slot }}</div>
            BLADE;
    }
}
