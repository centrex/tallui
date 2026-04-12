<?php

declare(strict_types=1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class Menu extends Component
{
    public string $sizeClass;

    /** @var array<int, array<string, mixed>> */
    public array $normalizedItems;

    public function __construct(
        public array $items = [],
        public bool $horizontal = false,
        public string $size = '',
        public string $color = '',
    ) {
        $this->sizeClass = match ($this->size) {
            'xs' => 'menu-xs',
            'sm' => 'menu-sm',
            'lg' => 'menu-lg',
            default => '',
        };

        $this->normalizedItems = $this->normalizeItems($this->items);
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeItems(array $items): array
    {
        return collect($items)
            ->filter(fn ($item) => is_array($item) && isset($item['label']))
            ->map(function (array $item): array {
                $children = collect($item['children'] ?? [])
                    ->filter(fn ($child) => is_array($child) && isset($child['label']))
                    ->map(function (array $child): array {
                        return [
                            'label' => (string) $child['label'],
                            'url' => $child['url'] ?? '#',
                            'icon' => $child['icon'] ?? null,
                            'active' => (bool) ($child['active'] ?? false),
                            'badge' => $child['badge'] ?? null,
                            'attributes' => is_array($child['attributes'] ?? null) ? $child['attributes'] : [],
                        ];
                    })
                    ->values()
                    ->all();

                $hasActiveChild = collect($children)->contains(fn (array $child) => $child['active'] === true);

                return [
                    'label' => (string) $item['label'],
                    'url' => $item['url'] ?? '#',
                    'icon' => $item['icon'] ?? null,
                    'active' => (bool) ($item['active'] ?? false),
                    'badge' => $item['badge'] ?? null,
                    'children' => $children,
                    'open' => (bool) ($item['open'] ?? $hasActiveChild),
                    'attributes' => is_array($item['attributes'] ?? null) ? $item['attributes'] : [],
                ];
            })
            ->values()
            ->all();
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
<ul
    {{ $attributes->class([
        'menu',
        'menu-horizontal' => $horizontal,
        $sizeClass => $sizeClass !== '',
        $color => $color !== '',
    ]) }}
>
    @foreach($normalizedItems as $item)
        @php
            $hasChildren = count($item['children']) > 0;
        @endphp

        @if($hasChildren)
            <li>
                <details @if($item['open']) open @endif>
                    <summary @class(['active' => $item['active']])>
                        @if($item['icon'])
                            <x-tallui-icon :name="$item['icon']" class="w-4 h-4 shrink-0" />
                        @endif

                        <span class="flex-1">{{ $item['label'] }}</span>

                        @if($item['badge'] !== null)
                            <span class="badge badge-sm badge-primary">{{ $item['badge'] }}</span>
                        @endif
                    </summary>

                    <ul>
                        @foreach($item['children'] as $child)
                            <li>
                                <a
                                    href="{{ $child['url'] }}"
                                    @class(['active' => $child['active']])
                                    @foreach($child['attributes'] as $attrKey => $attrValue)
                                        {{ $attrKey }}="{{ $attrValue }}"
                                    @endforeach
                                >
                                    @if($child['icon'])
                                        <x-tallui-icon :name="$child['icon']" class="w-4 h-4 shrink-0" />
                                    @endif

                                    <span class="flex-1">{{ $child['label'] }}</span>

                                    @if($child['badge'] !== null)
                                        <span class="badge badge-sm badge-primary">{{ $child['badge'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            </li>
        @else
            <li>
                <a
                    href="{{ $item['url'] }}"
                    @class(['active' => $item['active']])
                    @foreach($item['attributes'] as $attrKey => $attrValue)
                        {{ $attrKey }}="{{ $attrValue }}"
                    @endforeach
                >
                    @if($item['icon'])
                        <x-tallui-icon :name="$item['icon']" class="w-4 h-4 shrink-0" />
                    @endif

                    <span class="flex-1">{{ $item['label'] }}</span>

                    @if($item['badge'] !== null)
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