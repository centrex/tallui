<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ListItem extends Component
{
    public function __construct(
        // Primary content
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?string $value = null,
        public ?string $subValue = null,

        // Avatar / icon
        public ?string $avatar = null,     // image URL
        public ?string $avatarAlt = null,
        public ?string $icon = null,       // heroicon short name, e.g. o-user
        public string  $iconColor = 'text-base-content/40',

        // Link
        public ?string $link = null,
        public bool    $noWireNavigate = false,

        // Badges / labels
        public ?string $badge = null,      // text shown in a badge
        public string  $badgeType = 'neutral',

        // Layout
        public bool    $separator = true,  // border-b between items
        public bool    $compact = false,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            @php $tag = $link ? 'a' : 'div'; @endphp
            <{{ $tag }}
                @if($link)
                    href="{{ $link }}"
                    @if(!$noWireNavigate) wire:navigate @endif
                @endif
                @class([
                    'flex items-center gap-3 w-full text-left',
                    'py-3'       => !$compact,
                    'py-1.5'     => $compact,
                    'border-b border-base-200 last:border-0' => $separator,
                    'hover:bg-base-200/50 rounded-lg px-2 -mx-2 transition-colors cursor-pointer' => $link,
                ])
                {{ $attributes }}
            >
                {{-- Avatar or icon --}}
                @if($avatar)
                    <div class="avatar shrink-0">
                        <div @class(['rounded-full', 'w-9 h-9' => !$compact, 'w-7 h-7' => $compact])>
                            <img src="{{ $avatar }}" alt="{{ $avatarAlt ?? $title }}" />
                        </div>
                    </div>
                @elseif($icon)
                    <div @class([
                        'shrink-0 rounded-lg flex items-center justify-center bg-base-200',
                        'w-9 h-9' => !$compact,
                        'w-7 h-7' => $compact,
                    ])>
                        <x-tallui-icon :name="$icon" @class(['w-4 h-4', $iconColor]) />
                    </div>
                @endif

                {{-- Body slot or title/subtitle --}}
                <div class="flex-1 min-w-0">
                    @if(isset($slot) && !$slot->isEmpty())
                        {{ $slot }}
                    @else
                        @if($title)
                            <div @class(['text-sm font-medium truncate', 'text-xs' => $compact])>{{ $title }}</div>
                        @endif
                        @if($subtitle)
                            <div @class(['text-xs text-base-content/50 truncate', 'text-[11px]' => $compact])>{{ $subtitle }}</div>
                        @endif
                    @endif
                </div>

                {{-- Right side --}}
                @if($value || $badge || isset($actions))
                    <div class="shrink-0 flex flex-col items-end gap-1">
                        @if($value)
                            <span @class(['text-sm font-semibold', 'text-xs' => $compact])>{{ $value }}</span>
                        @endif
                        @if($subValue)
                            <span class="text-xs text-base-content/50">{{ $subValue }}</span>
                        @endif
                        @if($badge)
                            <x-tallui-badge :type="$badgeType" size="sm">{{ $badge }}</x-tallui-badge>
                        @endif
                        @if(isset($actions))
                            <div class="flex items-center gap-1">{{ $actions }}</div>
                        @endif
                    </div>
                @endif
            </{{ $tag }}>
            BLADE;
    }
}
