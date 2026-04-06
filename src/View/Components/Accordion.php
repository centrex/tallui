<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Accordion extends Component
{
    public function __construct(
        public string $name = 'accordion',
        public string $title = '',
        public bool $open = false,
        public string $variant = 'arrow',  // arrow | plus
        public string $color = '',       // bg-base-200 | bg-primary | etc.
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class([
                'collapse',
                "collapse-{$variant}" => $variant,
                'bg-base-200'         => !$color,
                $color                => $color,
            ]) {{ $attributes }}>
                <input type="checkbox" name="{{ $name }}" @checked($open) />
                <div class="collapse-title font-semibold">{{ $title }}</div>
                <div class="collapse-content text-sm">
                    {{ $slot }}
                </div>
            </div>
            BLADE;
    }
}
