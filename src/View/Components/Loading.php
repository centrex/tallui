<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Loading extends Component
{
    public function __construct(
        public string $variant = 'spinner', // spinner | dots | ring | ball | bars | infinity
        public string $size = 'md',      // xs | sm | md | lg
        public ?string $color = null,      // text-primary | text-secondary | etc.
        public ?string $label = null,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <span @class([
                'loading',
                "loading-{$variant}",
                "loading-{$size}",
                $color,
            ]) {{ $attributes }} aria-label="{{ $label ?? 'Loading' }}"></span>
            BLADE;
    }
}
