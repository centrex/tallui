<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Centrex\TallUi\Concerns\HasUuid;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    use HasUuid;

    /** @var array<int, array<string, mixed>> */
    public array $normalizedItems;

    /**
     * @param  array<int, array<string, mixed>>  $items  [['label'=>'', 'url'?, 'icon'?, 'color'?, 'badge'?, 'separator'?, 'sectionTitle'?, 'disabled'?, 'attributes'?], ...]
     */
    public function __construct(
        public array $items = [],
        public string $position = 'bottom-start', // bottom-start | bottom-end | top-start | top-end
        public ?string $id = null,
    ) {
        $this->generateUuid($id);

        $this->normalizedItems = collect($items)
            ->map(function (array $item): array {
                return [
                    'label'        => (string) ($item['label'] ?? ''),
                    'url'          => $item['url'] ?? null,
                    'icon'         => $item['icon'] ?? null,
                    'color'        => $item['color'] ?? null,
                    'badge'        => $item['badge'] ?? null,
                    'disabled'     => (bool) ($item['disabled'] ?? false),
                    'separator'    => (bool) ($item['separator'] ?? false),
                    'sectionTitle' => $item['sectionTitle'] ?? null,
                    'attributes'   => is_array($item['attributes'] ?? null) ? $item['attributes'] : [],
                ];
            })
            ->values()
            ->all();
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    open: false,
                    panelStyle: 'display:none',
                    init() {
                        const reposition = () => { if (this.open) this.updatePanelPosition(); };
                        window.addEventListener('resize', reposition);
                        window.addEventListener('scroll', reposition, true);
                        document.addEventListener('mousedown', (event) => this.handleDocumentClick(event));
                    },
                    handleDocumentClick(event) {
                        if (!this.open) return;
                        if (this.$refs.trigger?.contains(event.target) || this.$refs.panel?.contains(event.target)) return;
                        this.close();
                    },
                    toggle() { this.open ? this.close() : this.openPanel(); },
                    openPanel() {
                        this.open = true;
                        this.$nextTick(() => {
                            this.updatePanelPosition();
                            this.$refs.panel?.querySelector('[role=menuitem]')?.focus();
                        });
                    },
                    close() {
                        this.open = false;
                        this.$refs.trigger?.querySelector('button, a')?.focus();
                    },
                    focusNext(delta) {
                        const items = [...(this.$refs.panel?.querySelectorAll('[role=menuitem]') ?? [])];
                        if (!items.length) return;
                        const current = items.indexOf(document.activeElement);
                        const next = (current + delta + items.length) % items.length;
                        items[next]?.focus();
                    },
                    updatePanelPosition() {
                        if (!this.open || !this.$refs.trigger) {
                            this.panelStyle = 'display:none';
                            return;
                        }

                        const rect = this.$refs.trigger.getBoundingClientRect();
                        const spacing = 4;
                        const alignEnd = '{{ $position }}'.endsWith('end');
                        const openUpward = '{{ $position }}'.startsWith('top');
                        const verticalStyle = openUpward
                            ? 'bottom:' + (window.innerHeight - rect.top + spacing) + 'px'
                            : 'top:' + (rect.bottom + spacing) + 'px';
                        const horizontalStyle = alignEnd
                            ? 'right:' + (window.innerWidth - rect.right) + 'px'
                            : 'left:' + rect.left + 'px';

                        this.panelStyle = ['display:block', verticalStyle, horizontalStyle, 'min-width:' + rect.width + 'px'].join(';');
                    },
                }"
                @keydown.escape.window="if (open) close()"
                class="inline-block"
                {{ $attributes }}
            >
                <div x-ref="trigger" @click="toggle()" @keydown.arrow-down.prevent="openPanel()">
                    {{ $trigger ?? $slot }}
                </div>

                <template x-teleport="body">
                    <div
                        x-ref="panel"
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                        role="menu"
                        aria-orientation="vertical"
                        :aria-label="'{{ $uuid }}'"
                        @keydown.arrow-down.prevent="focusNext(1)"
                        @keydown.arrow-up.prevent="focusNext(-1)"
                        class="fixed z-[9999] py-1 bg-base-100 border border-base-300 rounded-box shadow-lg min-w-[10rem]"
                        :style="panelStyle"
                        style="display:none"
                    >
                        @foreach($normalizedItems as $item)
                            @if($item['separator'])
                                <div class="divider my-1" role="separator"></div>
                            @elseif($item['sectionTitle'])
                                <div class="px-3 py-1.5 text-xs font-semibold text-base-content/40 uppercase tracking-wide">{{ $item['sectionTitle'] }}</div>
                            @else
                                @php $tag = ($item['url'] && !$item['disabled']) ? 'a' : 'button'; @endphp
                                <{{ $tag }}
                                    @if($tag === 'a') href="{{ $item['url'] }}" @else type="button" @endif
                                    role="menuitem"
                                    tabindex="-1"
                                    @if($item['disabled']) disabled aria-disabled="true" @endif
                                    @click="close()"
                                    @foreach($item['attributes'] as $attrKey => $attrValue)
                                        {{ $attrKey }}="{{ $attrValue }}"
                                    @endforeach
                                    @class([
                                        'w-full flex items-center gap-2 px-3 py-2 text-sm text-left transition-colors',
                                        'hover:bg-base-200 focus:bg-base-200 outline-none' => !$item['disabled'],
                                        'opacity-40 cursor-not-allowed' => $item['disabled'],
                                        "text-{$item['color']}" => $item['color'],
                                    ])
                                >
                                    @if($item['icon'])
                                        <x-tallui-icon :name="$item['icon']" class="w-4 h-4 shrink-0" />
                                    @endif
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                    @if($item['badge'] !== null)
                                        <span class="badge badge-sm">{{ $item['badge'] }}</span>
                                    @endif
                                </{{ $tag }}>
                            @endif
                        @endforeach
                    </div>
                </template>
            </div>
            BLADE;
    }
}
