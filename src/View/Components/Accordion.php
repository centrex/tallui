<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Centrex\TallUi\Concerns\HasUuid;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Accordion extends Component
{
    use HasUuid;

    public function __construct(
        public string $name = 'accordion',
        public string $title = '',
        public bool $open = false,
        public string $variant = 'arrow',  // arrow | plus
        public string $color = '',       // bg-base-200 | bg-primary | etc.
    ) {
        $this->generateUuid();
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class([
                'collapse',
                "collapse-{$variant}" => $variant,
                'bg-base-200'         => !$color,
                $color                => $color,
            ]) {{ $attributes }}>
                <input id="{{ $uuid }}" type="checkbox" name="{{ $name }}" @checked($open) />
                <label for="{{ $uuid }}" class="collapse-title font-semibold">{{ $title }}</label>
                <div class="collapse-content text-sm">
                    {{ $slot }}
                </div>
            </div>
            BLADE;
    }
}
