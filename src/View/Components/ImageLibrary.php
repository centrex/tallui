<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageLibrary extends Component
{
    public function __construct(
        public array $images = [],       // [['src','alt'?,'caption'?,'id'?], ...]
        public bool $multiple = false,   // allow multi-select
        public array $selected = [],     // pre-selected ids/srcs
        public string $fit = 'cover',
        public string $height = 'h-32',
        public int $columns = 4,
        public ?string $name = null,     // if set, renders hidden inputs for form use
        public bool $selectable = true,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    images: {{ Js::from($images) }},
                    selected: {{ Js::from($selected) }},
                    lightbox: false,
                    current: 0,
                    multiple: {{ $multiple ? 'true' : 'false' }},
                    selectable: {{ $selectable ? 'true' : 'false' }},

                    isSelected(img) {
                        const key = img.id ?? img.src;
                        return this.selected.includes(String(key));
                    },
                    toggle(img) {
                        if (!this.selectable) return;
                        const key = String(img.id ?? img.src);
                        if (this.multiple) {
                            this.isSelected(img) ? this.selected.splice(this.selected.indexOf(key), 1) : this.selected.push(key);
                        } else {
                            this.selected = this.isSelected(img) ? [] : [key];
                        }
                    },
                    openLightbox(i) { this.current = i; this.lightbox = true; },
                    closeLightbox() { this.lightbox = false; },
                    next() { this.current = (this.current + 1) % this.images.length; },
                    prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
                }"
                @keydown.escape.window="closeLightbox()"
                @keydown.arrow-left.window="if (lightbox) prev()"
                @keydown.arrow-right.window="if (lightbox) next()"
                {{ $attributes }}
            >
                {{-- Grid --}}
                <div class="grid gap-2 grid-cols-2 sm:grid-cols-{{ $columns }}">
                    <template x-for="(img, i) in images" :key="img.id ?? img.src">
                        <div
                            class="relative overflow-hidden rounded-lg cursor-pointer group {{ $height }}"
                            :class="{
                                'ring-2 ring-primary ring-offset-1': selectable && isSelected(img),
                                'opacity-70': selectable && !isSelected(img) && selected.length > 0
                            }"
                            @click="toggle(img)"
                            @dblclick.stop="openLightbox(i)"
                        >
                            <img
                                :src="img.src"
                                :alt="img.alt || ''"
                                class="w-full h-full object-{{ $fit }} transition-transform duration-300 group-hover:scale-105"
                                loading="lazy"
                            />

                            {{-- Selection badge --}}
                            <template x-if="selectable && isSelected(img)">
                                <div class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-primary flex items-center justify-center shadow">
                                    <svg class="w-3 h-3 text-primary-content" viewBox="0 0 16 16" fill="currentColor">
                                        <path fill-rule="evenodd" d="M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 01-1.06 0L2.22 9.28a.75.75 0 011.06-1.06L6 10.94l6.72-6.72a.75.75 0 011.06 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </template>

                            {{-- Caption hover overlay --}}
                            <template x-if="img.caption">
                                <div class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-xs px-2 py-1 translate-y-full group-hover:translate-y-0 transition-transform duration-200" x-text="img.caption"></div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Selection count bar --}}
                <template x-if="selectable && selected.length > 0">
                    <div class="flex items-center justify-between mt-2 px-1">
                        <span class="text-xs text-base-content/60" x-text="`${selected.length} selected`"></span>
                        <button type="button" @click="selected = []" class="text-xs text-error hover:underline">Clear</button>
                    </div>
                </template>

                {{-- Hidden inputs --}}
                @if($name)
                    <template x-for="val in selected" :key="val">
                        <input type="hidden" name="{{ $name }}{{ $multiple ? '[]' : '' }}" :value="val" />
                    </template>
                @endif

                {{-- Lightbox --}}
                <div
                    x-show="lightbox"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center"
                    @click.self="closeLightbox()"
                    style="display:none"
                >
                    <button @click="prev()" class="absolute left-4 btn btn-circle btn-ghost text-white opacity-70 hover:opacity-100">❮</button>

                    <div class="max-w-5xl max-h-[90vh] flex flex-col items-center gap-3 px-16">
                        <img
                            :src="images[current]?.src"
                            :alt="images[current]?.alt || ''"
                            class="max-w-full max-h-[82vh] object-contain rounded-lg shadow-2xl"
                        />
                        <div class="flex items-center gap-4">
                            <p x-show="images[current]?.caption" x-text="images[current]?.caption" class="text-white/70 text-sm"></p>
                            <p class="text-white/40 text-xs" x-text="`${current + 1} / ${images.length}`"></p>
                        </div>
                    </div>

                    <button @click="next()" class="absolute right-4 btn btn-circle btn-ghost text-white opacity-70 hover:opacity-100">❯</button>
                    <button @click="closeLightbox()" class="absolute top-3 right-3 btn btn-sm btn-circle btn-ghost text-white">✕</button>
                </div>
            </div>
            BLADE;
    }
}
