<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MenuItem extends Component
{
    public string $resolvedBadgeType;

    public function __construct(
        public ?string $label = null,
        public ?string $icon = null,
        public ?string $link = null,
        public bool $active = false,
        public bool $wireNavigate = true,
        public ?string $badge = null,
        public string $badgeType = 'primary',
        public bool $separator = false,
        public ?string $sectionTitle = null,
        public bool $asButton = false,
        public string $buttonType = 'button',
        public bool $disabled = false,
    ) {
        $this->resolvedBadgeType = match ($this->badgeType) {
            'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error' => $this->badgeType,
            default                                                                            => 'primary',
        };
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
<li>
    @if($separator)
        <div class="menu-title mt-2 first:mt-0">
            @if($sectionTitle)
                <span>{{ $sectionTitle }}</span>
            @endif
        </div>
    @elseif($link)
        <a
            href="{{ $link }}"
            @if($wireNavigate && !$disabled) wire:navigate @endif
            @if($disabled) aria-disabled="true" tabindex="-1" @endif
            @class([
                'active' => $active,
                'pointer-events-none opacity-60' => $disabled,
            ])
            {{ $attributes }}
        >
            @if($icon)
                <x-tallui-icon :name="$icon" class="w-4 h-4 shrink-0" />
            @endif

            @if($label)
                <span class="flex-1">{{ $label }}</span>
            @endif

            {{ $slot }}

            @if($badge)
                <x-tallui-badge :type="$resolvedBadgeType" size="sm">{{ $badge }}</x-tallui-badge>
            @endif
        </a>
    @elseif($asButton)
        <button
            type="{{ $buttonType }}"
            @disabled($disabled)
            @class([
                'active' => $active,
                'w-full text-left',
            ])
            {{ $attributes }}
        >
            @if($icon)
                <x-tallui-icon :name="$icon" class="w-4 h-4 shrink-0" />
            @endif

            @if($label)
                <span class="flex-1">{{ $label }}</span>
            @endif

            {{ $slot }}

            @if($badge)
                <x-tallui-badge :type="$resolvedBadgeType" size="sm">{{ $badge }}</x-tallui-badge>
            @endif
        </button>
    @else
        <div
            @class([
                'active' => $active,
                'flex items-center gap-2',
                'opacity-60' => $disabled,
            ])
            {{ $attributes }}
        >
            @if($icon)
                <x-tallui-icon :name="$icon" class="w-4 h-4 shrink-0" />
            @endif

            @if($label)
                <span class="flex-1">{{ $label }}</span>
            @endif

            {{ $slot }}

            @if($badge)
                <x-tallui-badge :type="$resolvedBadgeType" size="sm">{{ $badge }}</x-tallui-badge>
            @endif
        </div>
    @endif
</li>
BLADE;
    }
}
