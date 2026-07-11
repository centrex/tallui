<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Kbd extends Component
{
    public function __construct(
        public ?string $key = null,
        public string $size = 'md',   // xs | sm | md | lg
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <kbd @class(['kbd', "kbd-{$size}" => $size]) {{ $attributes }}>{{ $key ?? $slot }}</kbd>
            BLADE;
    }
}
