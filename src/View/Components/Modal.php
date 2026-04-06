<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public string $id = 'modal',
        public string $title = '',
        public string $size = 'md',   // sm | md | lg | xl | full
        public bool $closeable = true,   // show X button + close on backdrop click
        public ?string $icon = null,   // Heroicon in the title bar
        public string $iconColor = 'text-primary',
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            {{--
                Usage:
                  <x-tallui-modal id="confirm-delete" title="Delete user?" icon="o-trash" icon-color="text-error">
                      <x-slot:trigger>
                          <button class="btn btn-error" @click="$dispatch('open-modal', 'confirm-delete')">Delete</button>
                      </x-slot:trigger>

                      <p>Are you sure you want to delete this user?</p>

                      <x-slot:footer>
                          <button class="btn" @click="$dispatch('close-modal', 'confirm-delete')">Cancel</button>
                          <button class="btn btn-error" wire:click="delete">Yes, delete</button>
                      </x-slot:footer>
                  </x-tallui-modal>
            --}}
            <div
                x-data="{ open: false }"
                @open-modal.window="if ($event.detail === '{{ $id }}') open = true"
                @close-modal.window="if ($event.detail === '{{ $id }}') open = false"
                @keydown.escape.window="if (open && {{ $closeable ? 'true' : 'false' }}) open = false"
            >
                {{-- Optional inline trigger slot --}}
                @if(isset($trigger))
                    <span @click="open = true">{{ $trigger }}</span>
                @endif

                {{-- Backdrop --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[9990] bg-black/50"
                    @if($closeable) @click="open = false" @endif
                    style="display:none"
                ></div>

                {{-- Modal panel --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    @class([
                        'fixed inset-0 z-[9991] flex items-center justify-center p-4',
                    ])
                    style="display:none"
                >
                    <div
                        @class([
                            'bg-base-100 rounded-2xl shadow-2xl w-full flex flex-col max-h-[90vh]',
                            'max-w-sm'  => $size === 'sm',
                            'max-w-lg'  => $size === 'md',
                            'max-w-2xl' => $size === 'lg',
                            'max-w-4xl' => $size === 'xl',
                            'max-w-full h-full rounded-none' => $size === 'full',
                        ])
                        @click.stop
                    >
                        {{-- Header --}}
                        @if($title || $closeable || $icon)
                            <div class="flex items-center justify-between px-6 py-4 border-b border-base-200 shrink-0">
                                <div class="flex items-center gap-3">
                                    @if($icon)
                                        <span class="{{ $iconColor }}">
                                            <x-tallui-icon :name="$icon" class="w-5 h-5" />
                                        </span>
                                    @endif
                                    @if($title)
                                        <h3 class="font-semibold text-lg">{{ $title }}</h3>
                                    @endif
                                </div>
                                @if($closeable)
                                    <button
                                        @click="open = false"
                                        class="btn btn-ghost btn-sm btn-circle text-base-content/50 hover:text-base-content"
                                        aria-label="Close"
                                    >
                                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @endif

                        {{-- Body --}}
                        <div class="px-6 py-5 overflow-y-auto flex-1 text-sm">
                            {{ $slot }}
                        </div>

                        {{-- Footer --}}
                        @if(isset($footer))
                            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-base-200 shrink-0">
                                {{ $footer }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            BLADE;
    }
}
