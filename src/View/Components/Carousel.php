<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Carousel extends Component
{
    public string $id;

    public function __construct(
        public array  $items      = [], // [['src','alt'?,'caption'?], ...]
        public bool   $arrows     = true,
        public bool   $indicators = true,
        public bool   $autoplay   = false,
        public int    $interval   = 3000,
        public string $fit        = 'cover', // cover | contain
        public string $height     = 'h-64',
    ) {
        $this->id = 'carousel-' . uniqid();
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    current: 0,
                    total: {{ count($items) }},
                    autoplay: {{ $autoplay ? 'true' : 'false' }},
                    interval: {{ $interval }},
                    timer: null,
                    start() {
                        if (this.autoplay) this.timer = setInterval(() => this.next(), this.interval);
                    },
                    stop()  { clearInterval(this.timer); },
                    next()  { this.current = (this.current + 1) % this.total; },
                    prev()  { this.current = (this.current - 1 + this.total) % this.total; },
                    go(i)   { this.current = i; },
                }"
                x-init="start()"
                @mouseenter="stop()"
                @mouseleave="start()"
                class="relative w-full overflow-hidden rounded-box {{ $height }}"
                {{ $attributes }}
            >
                {{-- Slides --}}
                @foreach($items as $i => $item)
                    <div
                        class="absolute inset-0 transition-opacity duration-500"
                        :class="current === {{ $i }} ? 'opacity-100 z-10' : 'opacity-0 z-0'"
                    >
                        <img
                            src="{{ $item['src'] }}"
                            alt="{{ $item['alt'] ?? '' }}"
                            class="w-full h-full object-{{ $fit }}"
                        />
                        @if(isset($item['caption']))
                            <div class="absolute bottom-0 inset-x-0 bg-black/40 text-white text-sm px-4 py-2">
                                {{ $item['caption'] }}
                            </div>
                        @endif
                    </div>
                @endforeach

                {{ $slot }}

                {{-- Arrows --}}
                @if($arrows)
                    <button @click="prev()" class="absolute left-2 top-1/2 -translate-y-1/2 z-20 btn btn-circle btn-sm btn-ghost bg-black/30 hover:bg-black/50 text-white border-0">❮</button>
                    <button @click="next()" class="absolute right-2 top-1/2 -translate-y-1/2 z-20 btn btn-circle btn-sm btn-ghost bg-black/30 hover:bg-black/50 text-white border-0">❯</button>
                @endif

                {{-- Indicators --}}
                @if($indicators && count($items) > 1)
                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-20 flex gap-1.5">
                        @foreach($items as $i => $item)
                            <button
                                @click="go({{ $i }})"
                                :class="current === {{ $i }} ? 'bg-white' : 'bg-white/40'"
                                class="w-2 h-2 rounded-full transition-colors"
                            ></button>
                        @endforeach
                    </div>
                @endif
            </div>
            BLADE;
    }
}
