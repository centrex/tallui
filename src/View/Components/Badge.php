<?php

namespace Centrex\TallUi\View\Components;

use Illuminate\View\Component;
class Badge extends Component
{
    public function __construct(
        public string $color = 'neutral',
    ) {}

    public function render()
    {
        return <<<'BLADE'
            <div {{ $attributes->class(["badge badge-{$color}"]) }}>
                {{ $slot }}
            </div>
        BLADE;
    }
}