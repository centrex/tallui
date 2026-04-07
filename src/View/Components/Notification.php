<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Flash-message notification that auto-reads from the session.
 * Supports both manual messages and Livewire-dispatched events.
 *
 * Usage (Blade):
 *   <x-tallui-notification />
 *
 * Usage (Livewire component):
 *   $this->dispatch('notify', type: 'success', message: 'Saved!');
 */
class Notification extends Component
{
    public function __construct(
        public int $timeout = 4000,       // ms before auto-dismiss
        public string $position = 'top-right',  // top-right | top-left | bottom-right | bottom-left | top-center
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    notifications: [],
                    add(type, message, timeout) {
                        const id = Date.now();
                        this.notifications.push({ id, type, message, visible: true });
                        if (timeout > 0) {
                            setTimeout(() => this.remove(id), timeout);
                        }
                    },
                    remove(id) {
                        const n = this.notifications.find(n => n.id === id);
                        if (n) n.visible = false;
                        setTimeout(() => {
                            this.notifications = this.notifications.filter(n => n.id !== id);
                        }, 300);
                    }
                }"
                @notify.window="add($event.detail.type ?? 'info', $event.detail.message, {{ $timeout }})"
                @class([
                    'fixed z-[9999] flex flex-col gap-2 w-full max-w-sm pointer-events-none',
                    'top-4 right-4'    => $position === 'top-right',
                    'top-4 left-4'     => $position === 'top-left',
                    'bottom-4 right-4' => $position === 'bottom-right',
                    'bottom-4 left-4'  => $position === 'bottom-left',
                    'top-4 left-1/2 -translate-x-1/2' => $position === 'top-center',
                ])
            >
                {{-- Session flash messages --}}
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, {{ $timeout }})"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="alert alert-success shadow-lg pointer-events-auto rounded-2xl text-sm" role="alert">
                        <x-tallui-icon name="heroicon-o-check-circle" class="w-5 h-5 shrink-0" />
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, {{ $timeout }})"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="alert alert-error shadow-lg pointer-events-auto rounded-2xl text-sm" role="alert">
                        <x-tallui-icon name="heroicon-o-x-circle" class="w-5 h-5 shrink-0" />
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, {{ $timeout }})"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="alert alert-warning shadow-lg pointer-events-auto rounded-2xl text-sm" role="alert">
                        <x-tallui-icon name="heroicon-o-exclamation-triangle" class="w-5 h-5 shrink-0" />
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if(session('info') || session('message'))
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, {{ $timeout }})"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="alert alert-info shadow-lg pointer-events-auto rounded-2xl text-sm" role="alert">
                        <x-tallui-icon name="heroicon-o-information-circle" class="w-5 h-5 shrink-0" />
                        <span>{{ session('info') ?? session('message') }}</span>
                    </div>
                @endif

                {{-- Livewire-dispatched notifications --}}
                <template x-for="n in notifications" :key="n.id">
                    <div
                        x-show="n.visible"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        :class="{
                            'alert-success': n.type === 'success',
                            'alert-error': n.type === 'error',
                            'alert-warning': n.type === 'warning',
                            'alert-info': n.type === 'info',
                        }"
                        class="alert shadow-lg pointer-events-auto rounded-2xl text-sm"
                        role="alert"
                    >
                        <svg x-show="n.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg x-show="n.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg x-show="n.type === 'warning'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <svg x-show="n.type === 'info'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="n.message"></span>
                        <button @click="remove(n.id)" class="btn btn-ghost btn-xs btn-circle ml-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                </template>
            </div>
            BLADE;
    }
}
