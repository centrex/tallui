<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Avatar extends Component
{
    public string $sizeClass;

    public function __construct(
        public ?string $src      = null,
        public string  $alt      = '',
        public ?string $initials = null,
        public string  $size     = 'md',    // xs | sm | md | lg
        public string  $shape    = 'circle', // circle | square | rounded
        public string  $color    = 'bg-neutral text-neutral-content',
        public bool    $online   = false,
        public bool    $offline  = false,
        public ?string $badge    = null,
    ) {
        $this->sizeClass = match ($size) {
            'xs' => 'w-6',
            'sm' => 'w-8',
            'lg' => 'w-16',
            default => 'w-12',
        };
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class([
                'avatar',
                'placeholder' => !$src,
                'online'  => $online,
                'offline' => $offline,
            ])>
                <div @class([
                    $sizeClass,
                    'rounded-full'    => $shape === 'circle',
                    'rounded-none'    => $shape === 'square',
                    'rounded-lg'      => $shape === 'rounded',
                    $color            => !$src,
                ])>
                    @if($src)
                        <img src="{{ $src }}" alt="{{ $alt }}" />
                    @elseif($initials)
                        <span class="text-xs font-bold uppercase">{{ mb_strtoupper(mb_substr($initials, 0, 2)) }}</span>
                    @else
                        <svg class="w-1/2 h-1/2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    @endif
                </div>
                @if($badge !== null)
                    <span class="badge badge-sm badge-primary absolute -top-1 -right-1">{{ $badge }}</span>
                @endif
            </div>
            BLADE;
    }
}
