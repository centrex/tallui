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
        public string $size = '',         // xs | sm | md | lg | xl
    ) {
        if ($this->active === '' && $tabs !== []) {
            $this->active = $tabs[0]['id'];
        }
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    activeTab: '{{ $active }}',
                    tabIds: {{ json_encode(array_column($tabs, 'id')) }},
                    focusTab(delta) {
                        const i = this.tabIds.indexOf(this.activeTab);
                        const next = this.tabIds[(i + delta + this.tabIds.length) % this.tabIds.length];
                        this.activeTab = next;
                        this.$nextTick(() => this.$refs['tab-' + next]?.focus());
                    },
                }"
                {{ $attributes }}
            >
                @php
                    // DaisyUI 5 renamed these modifiers (v3/v4: tabs-bordered/-lifted/-boxed)
                    $variantClass = match ($variant) {
                        'lifted' => 'tabs-lift',
                        'boxed'  => 'tabs-box',
                        default  => 'tabs-border',
                    };
                @endphp

                {{-- Tab headers --}}
                <div role="tablist" @class(['tabs', $variantClass, "tabs-{$size}" => $size])>
                    @foreach($tabs as $tab)
                        <button
                            x-ref="tab-{{ $tab['id'] }}"
                            role="tab"
                            id="tab-{{ $tab['id'] }}"
                            aria-controls="panel-{{ $tab['id'] }}"
                            :aria-selected="activeTab === '{{ $tab['id'] }}' ? 'true' : 'false'"
                            :tabindex="activeTab === '{{ $tab['id'] }}' ? 0 : -1"
                            @class(['tab', 'tab-active' => false])
                            :class="{ 'tab-active': activeTab === '{{ $tab['id'] }}' }"
                            @click="activeTab = '{{ $tab['id'] }}'"
                            @keydown.right.prevent="focusTab(1)"
                            @keydown.left.prevent="focusTab(-1)"
                            type="button"
                        >
                            @if(isset($tab['icon']))
                                <x-tallui-icon :name="$tab['icon']" class="w-4 h-4 mr-1.5" />
                            @endif
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>

                @php
                    // Note: this can't be a closure (e.g. collect()->contains(fn...)) —
                    // arrow functions only auto-capture variables whose names are
                    // statically visible in the function body, so a dynamically-named
                    // variable-variable like ${$tab['id']} would silently never resolve.
                    $__tallUiDefinedVars = get_defined_vars();
                    $hasNamedSlots = false;
                    foreach ($tabs as $__tallUiTab) {
                        $__tallUiSlot = $__tallUiDefinedVars[$__tallUiTab['id']] ?? null;
                        if ($__tallUiSlot !== null && trim((string) $__tallUiSlot) !== '') {
                            $hasNamedSlots = true;
                            break;
                        }
                    }
                @endphp

                @if($hasNamedSlots)
                    {{-- Tab panels via named slots, e.g. <x-slot:general>...</x-slot:general> --}}
                    @foreach($tabs as $tab)
                        <div
                            x-show="activeTab === '{{ $tab['id'] }}'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            role="tabpanel"
                            id="panel-{{ $tab['id'] }}"
                            aria-labelledby="tab-{{ $tab['id'] }}"
                            tabindex="0"
                            class="py-4"
                        >
                            {{ ${$tab['id']} ?? '' }}
                        </div>
                    @endforeach
                @else
                    {{-- Fallback: default slot content drives its own x-show="activeTab === '...'" panels --}}
                    <div class="py-4">{{ $slot }}</div>
                @endif
            </div>
            BLADE;
    }
}
