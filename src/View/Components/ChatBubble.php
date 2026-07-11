<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ChatBubble extends Component
{
    public function __construct(
        public string $position = 'start',   // start | end
        public ?string $avatar = null,
        public ?string $name = null,
        public ?string $time = null,
        public ?string $status = null,       // e.g. "Delivered", "Seen"
        public string $color = '',           // '' | primary | secondary | accent | info | success | warning | error
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class(['chat', "chat-{$position}"]) {{ $attributes }}>
                @if($avatar)
                    <div class="chat-image avatar">
                        <div class="w-10 rounded-full">
                            <img src="{{ $avatar }}" alt="{{ $name ?? '' }}" />
                        </div>
                    </div>
                @endif

                @if($name || $time)
                    <div class="chat-header">
                        {{ $name }}
                        @if($time)
                            <time class="text-xs opacity-50">{{ $time }}</time>
                        @endif
                    </div>
                @endif

                <div @class(['chat-bubble', "chat-bubble-{$color}" => $color])>
                    {{ $slot }}
                </div>

                @if($status)
                    <div class="chat-footer opacity-50 text-xs">{{ $status }}</div>
                @endif
            </div>
            BLADE;
    }
}
