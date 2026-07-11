<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Skeleton extends Component
{
    public function __construct(
        public string $variant = 'text',   // text | circle | rect
        public string $width = 'w-full',
        public string $height = 'h-4',
        public int $lines = 1,             // number of stacked bars, only for variant=text
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            @if($variant === 'text' && $lines > 1)
                <div class="flex flex-col gap-2" role="status" aria-label="Loading" {{ $attributes }}>
                    @for($i = 0; $i < $lines; $i++)
                        <div @class([
                            'skeleton h-4',
                            $width,
                            'w-4/5' => $i === $lines - 1 && $lines > 1,
                        ])></div>
                    @endfor
                </div>
            @else
                <div
                    role="status"
                    aria-label="Loading"
                    @class([
                        'skeleton',
                        $width,
                        $height,
                        'rounded-full' => $variant === 'circle',
                    ])
                    {{ $attributes }}
                ></div>
            @endif
            BLADE;
    }
}
