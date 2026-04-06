<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public string $alertClass;

    public function __construct(
        public string $type = 'info',   // info | success | warning | error
        public ?string $title = null,
        public ?string $icon = null,
        public bool $dismissible = false,
    ) {
        $this->alertClass = match ($type) {
            'success' => 'alert-success',
            'warning' => 'alert-warning',
            'error'   => 'alert-error',
            default   => 'alert-info',
        };

        $this->icon ??= match ($type) {
            'success' => 'o-check-circle',
            'warning' => 'o-exclamation-triangle',
            'error'   => 'o-x-circle',
            default   => 'o-information-circle',
        };
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{ show: true }"
                x-show="show"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @class(['alert', $alertClass])
                role="alert"
                {{ $attributes->except(['type','title','icon','dismissible']) }}
            >
                <x-tallui-icon :name="$icon" class="w-5 h-5 shrink-0" />

                <div class="flex-1">
                    @if($title)
                        <p class="font-semibold">{{ $title }}</p>
                    @endif
                    @if($slot->isNotEmpty())
                        <div class="text-sm {{ $title ? 'opacity-80' : '' }}">{{ $slot }}</div>
                    @endif
                </div>

                @if($dismissible)
                    <button @click="show = false" class="btn btn-ghost btn-xs btn-circle">
                        <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                @endif
            </div>
            BLADE;
    }
}
