<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Group extends Component
{
    public function __construct(
        public bool $vertical = false,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class(['join', 'join-vertical' => $vertical]) {{ $attributes }}>
                {{ $slot }}
            </div>
            BLADE;
    }
}
