<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tab extends Component
{
    public function __construct(
        public array $tabs = [], // [['id' => '', 'label' => '', 'icon'? => ''], ...]
        public string $active = '',
        public string $variant = 'bordered', // bordered | lifted | boxed
        public string $size = '',         // xs | sm | lg
    ) {
        if ($this->active === '' && count($tabs) > 0) {
            $this->active = $tabs[0]['id'];
        }
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{ activeTab: '{{ $active }}' }"
                {{ $attributes }}
            >
                {{-- Tab headers --}}
                <div @class(['tabs', "tabs-{$variant}", "tabs-{$size}" => $size])>
                    @foreach($tabs as $tab)
                        <button
                            @class(['tab', 'tab-active' => false])
                            :class="{ 'tab-active': activeTab === '{{ $tab['id'] }}' }"
                            @click="activeTab = '{{ $tab['id'] }}'"
                            type="button"
                        >
                            @if(isset($tab['icon']))
                                <x-tallui-icon :name="$tab['icon']" class="w-4 h-4 mr-1.5" />
                            @endif
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>

                {{-- Tab panels --}}
                @foreach($tabs as $tab)
                    <div
                        x-show="activeTab === '{{ $tab['id'] }}'"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        class="py-4"
                    >
                        {{ $$tab['id'] ?? '' }}
                    </div>
                @endforeach

                {{-- Fallback: named slots not provided, render default slot in active panel --}}
                <div x-show="false">{{ $slot }}</div>
            </div>
            BLADE;
    }
}
