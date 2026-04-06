<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Steps extends Component
{
    public function __construct(
        public array $steps = [], // [['label' => '', 'color' => 'primary'?], ...]
        public int $current = 1,  // 1-based index of the active step
        public bool $vertical = false,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <ul @class(['steps', 'steps-vertical' => $vertical]) {{ $attributes }}>
                @foreach($steps as $i => $step)
                    @php
                        $stepNum   = $i + 1;
                        $isDone    = $stepNum <= $current;
                        $stepColor = $step['color'] ?? 'primary';
                    @endphp
                    <li @class([
                        'step',
                        "step-{$stepColor}" => $isDone,
                    ])>{{ $step['label'] }}</li>
                @endforeach
            </ul>
            BLADE;
    }
}
