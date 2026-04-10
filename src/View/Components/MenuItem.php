<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MenuItem extends Component
{
    public function __construct(
        public ?string $label = null,
        public ?string $icon = null,
        public ?string $link = null,
        public bool    $active = false,
        public bool    $noWireNavigate = false,
        public ?string $badge = null,
        public string  $badgeType = 'primary',
        // Separator / section title
        public bool    $separator = false,
        public ?string $sectionTitle = null,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            @if($separator)
                <li class="menu-title mt-2 first:mt-0">
                    @if($sectionTitle)
                        <span>{{ $sectionTitle }}</span>
                    @endif
                </li>
            @else
                <li>
                    @if($link)
                        <a
                            href="{{ $link }}"
                            @if(!$noWireNavigate) wire:navigate @endif
                            @class(['active' => $active])
                            {{ $attributes }}
                        >
                            @if($icon)
                                <x-tallui-icon :name="$icon" class="w-4 h-4" />
                            @endif
                            @if($label){{ $label }}@endif
                            {{ $slot }}
                            @if($badge)
                                <x-tallui-badge :type="$badgeType" size="sm">{{ $badge }}</x-tallui-badge>
                            @endif
                        </a>
                    @else
                        <span @class(['active' => $active]) {{ $attributes }}>
                            @if($icon)
                                <x-tallui-icon :name="$icon" class="w-4 h-4" />
                            @endif
                            @if($label){{ $label }}@endif
                            {{ $slot }}
                            @if($badge)
                                <x-tallui-badge :type="$badgeType" size="sm">{{ $badge }}</x-tallui-badge>
                            @endif
                        </span>
                    @endif
                </li>
            @endif
            BLADE;
    }
}
