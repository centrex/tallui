<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Illuminate\View\Component;

class Badge extends Component
{
    public string $colorClass;

    public function __construct(
        public string $color = 'neutral',
        public ?string $type = null,   // success | error | warning | info | neutral (alias for color)
        public string $size = '',      // xs | sm | md | lg
    ) {
        // $type is an alias for $color using DaisyUI semantic names
        $resolved        = $type ?? $color;
        $this->colorClass = match ($resolved) {
            'success'  => 'badge-success',
            'error'    => 'badge-error',
            'warning'  => 'badge-warning',
            'info'     => 'badge-info',
            'primary'  => 'badge-primary',
            'secondary' => 'badge-secondary',
            'accent'   => 'badge-accent',
            'ghost'    => 'badge-ghost',
            'outline'  => 'badge-outline',
            default    => 'badge-neutral',
        };
    }

    public function render()
    {
        return <<<'BLADE'
            <span {{ $attributes->class(['badge', $colorClass, "badge-{$size}" => $size]) }}>
                {{ $slot }}
            </span>
        BLADE;
    }
}
