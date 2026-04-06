<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public function __construct(
        public array $items = [], // [['label' => 'Home', 'url' => '/'], ...]
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div class="breadcrumbs text-sm" {{ $attributes }}>
                <ul>
                    @foreach($items as $item)
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
