<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Timeline extends Component
{
    public function __construct(
        public array $items = [], // [['time','title','description','color'?,'icon'?], ...]
        public bool $compact = false,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <ul @class(['timeline timeline-vertical', 'timeline-compact' => $compact]) {{ $attributes }}>
                @foreach($items as $item)
                    @php $color = $item['color'] ?? 'primary'; @endphp
                    <li>
                        @if(!$loop->first)<hr />@endif
                        <div class="timeline-start text-sm text-base-content/60">{{ $item['time'] ?? '' }}</div>
                        <div class="timeline-middle">
                            <div @class([
                                'w-5 h-5 rounded-full flex items-center justify-center',
                                "bg-{$color}",
                            ])>
                                @if(isset($item['icon']))
                                    <x-tallui-icon :name="$item['icon']" class="w-3 h-3 text-white" />
                                @else
                                    <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <circle cx="10" cy="10" r="4"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="timeline-end timeline-box">
                            <p class="font-semibold text-sm">{{ $item['title'] ?? '' }}</p>
                            @if(isset($item['description']))
                                <p class="text-sm text-base-content/70 mt-0.5">{{ $item['description'] }}</p>
                            @endif
                        </div>
                        @if(!$loop->last)<hr />@endif
                    </li>
                @endforeach
                {{ $slot }}
            </ul>
            BLADE;
    }
}
