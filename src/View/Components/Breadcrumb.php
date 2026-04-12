<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $resolvedItems;

    public function __construct(
        public array $items = [], // [['label' => 'Home', 'url' => '/'], ...]
        public array $links = [], // Backward/documented alias for $items
    ) {
        $source = $this->items !== [] ? $this->items : $this->links;

        $this->resolvedItems = array_values(array_filter(array_map(
            static function (mixed $item): ?array {
                if (!is_array($item) || !isset($item['label'])) {
                    return null;
                }

                $url = $item['url'] ?? $item['href'] ?? null;

                return [
                    'label' => (string) $item['label'],
                    'url'   => is_string($url) && $url !== '' ? $url : null,
                    'icon'  => isset($item['icon']) && is_string($item['icon']) ? $item['icon'] : null,
                ];
            },
            $source,
        )));
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div class="breadcrumbs text-sm" {{ $attributes }}>
                <ul>
                    @foreach($resolvedItems as $item)
                        <li>
                            @if(!$loop->last && isset($item['url']))
                                <a href="{{ $item['url'] }}" class="hover:text-primary transition-colors">
                                    @if(isset($item['icon']))
                                        <x-tallui-icon :name="$item['icon']" class="w-4 h-4 mr-1" />
                                    @endif
                                    {{ $item['label'] }}
                                </a>
                            @else
                                @if(isset($item['icon']))
                                    <x-tallui-icon :name="$item['icon']" class="w-4 h-4 mr-1" />
                                @endif
                                {{ $item['label'] }}
                            @endif
                        </li>
                    @endforeach
                    {{ $slot }}
                </ul>
            </div>
            BLADE;
    }
}
