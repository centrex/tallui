<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Swap extends Component
{
    public function __construct(
        public bool $active = false,
        public string $effect = 'rotate', // rotate | flip
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <label @class(['swap', "swap-{$effect}"]) {{ $attributes }}>
                <input type="checkbox" @checked($active) />
                <div class="swap-on">{{ $on }}</div>
                <div class="swap-off">{{ $off }}</div>
            </label>
            BLADE;
    }
}
