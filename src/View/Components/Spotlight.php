<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Spotlight extends Component
{
    public function __construct(
        public string  $placeholder = 'Search anything...',
        public string  $shortcut    = '/',
        public array   $items       = [], // [['label','description'?,'url'?,'icon'?,'group'?], ...]
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    open: false,
                    query: '',
                    items: {{ Js::from($items) }},
                    selected: 0,
                    get filtered() {
                        if (!this.query) return this.items;
                        const q = this.query.toLowerCase();
                        return this.items.filter(i =>
                            i.label.toLowerCase().includes(q) ||
                            (i.description || '').toLowerCase().includes(q)
                        );
                    },
                    show()  { this.open = true; this.$nextTick(() => this.$refs.input?.focus()); },
                    hide()  { this.open = false; this.query = ''; this.selected = 0; },
                    go()    {
                        const item = this.filtered[this.selected];
                        if (item?.url) window.location.href = item.url;
                        this.hide();
                    },
                    up()    { this.selected = Math.max(0, this.selected - 1); },
                    down()  { this.selected = Math.min(this.filtered.length - 1, this.selected + 1); },
                }"
                @keydown.window="{{ $shortcut === '/' ? \"if(event.key === '/' && !['INPUT','TEXTAREA'].includes(document.activeElement.tagName)){ event.preventDefault(); show(); }\" : '' }}"
                @keydown.window.meta.k.prevent="show()"
                @keydown.window.ctrl.k.prevent="show()"
            >
                {{-- Trigger button --}}
                <button @click="show()" class="btn btn-ghost btn-sm gap-2 text-base-content/60" {{ $attributes }}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    {{ $slot->isNotEmpty() ? $slot : 'Search...' }}
                    <kbd class="kbd kbd-xs">⌘K</kbd>
                </button>

                {{-- Modal overlay --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[9998] bg-black/50 flex items-start justify-center pt-24 px-4"
                    @click.self="hide()"
                    @keydown.escape.window="hide()"
                    style="display:none"
                >
                    <div class="w-full max-w-lg bg-base-100 rounded-2xl shadow-2xl border border-base-300 overflow-hidden">
                        {{-- Search input --}}
                        <div class="flex items-center gap-3 px-4 py-3 border-b border-base-200">
                            <svg class="w-5 h-5 text-base-content/40 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                x-ref="input"
                                x-model="query"
                                type="text"
                                placeholder="{{ $placeholder }}"
                                class="flex-1 bg-transparent outline-none text-sm"
                                @keydown.enter.prevent="go()"
                                @keydown.arrow-up.prevent="up()"
                                @keydown.arrow-down.prevent="down()"
                            />
                            <kbd class="kbd kbd-xs" @click="hide()">Esc</kbd>
                        </div>

                        {{-- Results --}}
                        <div class="max-h-80 overflow-y-auto py-2">
                            <template x-if="filtered.length === 0">
                                <p class="text-center text-sm text-base-content/50 py-8">No results for "<span x-text="query"></span>"</p>
                            </template>

                            <template x-for="(item, i) in filtered" :key="i">
                                <a
                                    :href="item.url || '#'"
                                    @click.prevent="selected = i; go()"
                                    @mouseenter="selected = i"
                                    :class="selected === i ? 'bg-primary text-primary-content' : 'hover:bg-base-200'"
                                    class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors"
                                >
                                    <span x-html="item.icon ? `<span class='w-5 h-5 opacity-70'>${item.icon}</span>` : '<span class=\'w-5 h-5\'></span>'"></span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium truncate" x-text="item.label"></p>
                                        <p class="text-xs opacity-60 truncate" x-show="item.description" x-text="item.description"></p>
                                    </div>
                                    <template x-if="item.group">
                                        <span class="badge badge-sm badge-ghost" x-text="item.group"></span>
                                    </template>
                                </a>
                            </template>
                        </div>

                        {{-- Footer hints --}}
                        <div class="border-t border-base-200 px-4 py-2 flex gap-4 text-xs text-base-content/40">
                            <span><kbd class="kbd kbd-xs">↑↓</kbd> navigate</span>
                            <span><kbd class="kbd kbd-xs">↵</kbd> select</span>
                            <span><kbd class="kbd kbd-xs">Esc</kbd> close</span>
                        </div>
                    </div>
                </div>
            </div>
            BLADE;
    }
}
