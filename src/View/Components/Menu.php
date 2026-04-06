<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Menu extends Component
{
    public function __construct(
        public array  $items      = [], // [['label','url'?,'icon'?,'active'?,'children'?], ...]
        public bool   $horizontal = false,
        public string $size       = '',  // xs | sm | lg
        public string $color      = '',  // bg-base-100 | bg-base-200 | etc.
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <ul @class([
                'menu',
                'menu-horizontal' => $horizontal,
                "menu-{$size}"    => $size,
                $color            => $color,
            ]) {{ $attributes }}>
                @foreach($items as $item)
                    @if(isset($item['children']) && count($item['children']))
                        <li>
                            <details>
                                <summary @class(['active' => $item['active'] ?? false])>
                                    @if(isset($item['icon']))
                                        <x-tallui-icon :name="$item['icon']" class="w-4 h-4" />
                                    @endif
                                    {{ $item['label'] }}
                                </summary>
                                <ul>
                                    @foreach($item['children'] as $child)
                                        <li>
                                            <a href="{{ $child['url'] ?? '#' }}"
                                               @class(['active' => $child['active'] ?? false])>
                                                @if(isset($child['icon']))
                                                    <x-tallui-icon :name="$child['icon']" class="w-4 h-4" />
                                                @endif
                                                {{ $child['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        </li>
                    @else
                        <li>
                            <a href="{{ $item['url'] ?? '#' }}"
                               @class(['active' => $item['active'] ?? false])>
                                @if(isset($item['icon']))
                                    <x-tallui-icon :name="$item['icon']" class="w-4 h-4" />
                                @endif
                                {{ $item['label'] }}
                                @if(isset($item['badge']))
                                    <span class="badge badge-sm badge-primary">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach
                {{ $slot }}
            </ul>
            BLADE;
    }
}
