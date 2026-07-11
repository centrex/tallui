<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageGallery extends Component
{
    public function __construct(
        public array $images = [], // [['src','alt'?,'caption'?], ...]
        public int $columns = 3,
        public bool $lightbox = true,
        public string $fit = 'cover', // cover | contain
        public string $height = 'h-48',
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    lightbox: false,
                    current: 0,
                    images: {{ Js::from($images) }},
                    open(i)  { this.current = i; this.lightbox = true; },
                    close()  { this.lightbox = false; },
                    next()   { this.current = (this.current + 1) % this.images.length; },
                    prev()   { this.current = (this.current - 1 + this.images.length) % this.images.length; },
                }"
                {{ $attributes }}
            >
                @php
                    // Tailwind's JIT scanner only recognizes complete literal class tokens,
                    // so `grid-cols-{{ $columns }}` (interpolated) is never generated. Map to
                    // a fixed set of literal classes instead.
                    $gridColsClass = match (true) {
                        $columns <= 1 => 'grid-cols-1',
                        $columns === 2 => 'grid-cols-2',
                        $columns === 3 => 'grid-cols-3',
                        $columns === 4 => 'grid-cols-4',
                        $columns === 5 => 'grid-cols-5',
                        default => 'grid-cols-6',
                    };
                @endphp

                {{-- Grid --}}
                <div class="grid gap-2 {{ $gridColsClass }}" role="{{ $lightbox ? 'list' : 'group' }}">
                    @foreach($images as $i => $img)
                        <div
                            class="relative overflow-hidden rounded-lg cursor-pointer group {{ $height }}"
                            @if($lightbox) @click="open({{ $i }})" role="listitem" tabindex="0" @keydown.enter="open({{ $i }})" @endif
                        >
                            <img
                                src="{{ $img['src'] }}"
                                alt="{{ $img['alt'] ?? '' }}"
                                class="w-full h-full object-{{ $fit }} transition-transform duration-300 group-hover:scale-105"
                            />
                            @if(isset($img['caption']))
                                <div class="absolute inset-x-0 bottom-0 bg-black/50 text-white text-xs px-2 py-1 translate-y-full group-hover:translate-y-0 transition-transform">
                                    {{ $img['caption'] }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Lightbox --}}
                @if($lightbox)
                    <div
                        x-show="lightbox"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center"
                        role="dialog"
                        aria-modal="true"
                        aria-label="Image lightbox"
                        x-init="$watch('lightbox', (v) => v && $nextTick(() => $el.querySelector('[data-lightbox-close]')?.focus()))"
                        @click.self="close()"
                        @keydown.escape.window="close()"
                        @keydown.arrow-left.window="prev()"
                        @keydown.arrow-right.window="next()"
                        style="display:none"
                    >
                        <button @click="prev()" aria-label="Previous image" class="absolute left-4 btn btn-circle btn-ghost text-white text-2xl">❮</button>

                        <div class="max-w-4xl max-h-[90vh] flex flex-col items-center gap-3" role="group" aria-roledescription="slide">
                            <img
                                :src="images[current]?.src"
                                :alt="images[current]?.alt || ''"
                                class="max-w-full max-h-[80vh] object-contain rounded-lg"
                            />
                            <p x-show="images[current]?.caption" x-text="images[current]?.caption" class="text-white/70 text-sm"></p>
                            <p class="text-white/40 text-xs" x-text="`${current + 1} / ${images.length}`" aria-live="polite"></p>
                        </div>

                        <button @click="next()" aria-label="Next image" class="absolute right-4 btn btn-circle btn-ghost text-white text-2xl">❯</button>
                        <button @click="close()" data-lightbox-close aria-label="Close lightbox" class="absolute top-4 right-4 btn btn-circle btn-ghost text-white">✕</button>
                    </div>
                @endif
            </div>
            BLADE;
    }
}
