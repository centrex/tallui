<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ToastContainer extends Component
{
    public function __construct(
        public string $position = 'end bottom', // DaisyUI toast position classes
        public int    $timeout  = 3000,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="tallUiToast()"
                x-init="init()"
                :class="'toast ' + position"
                style="z-index: 9999;"
            >
                <template x-for="(item, index) in toasts" :key="index">
                    <div
                        :class="'alert shadow-lg ' + item.css"
                        x-show="item.visible"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                    >
                        <span x-html="item.icon"></span>
                        <div>
                            <p class="font-semibold" x-text="item.title"></p>
                            <p class="text-sm opacity-80" x-show="item.description" x-text="item.description"></p>
                        </div>
                        <button @click="dismiss(index)" class="btn btn-ghost btn-xs btn-circle ml-2">✕</button>
                    </div>
                </template>
            </div>

            <script>
                function tallUiToast() {
                    return {
                        toasts: [],
                        position: '{{ $position }}',

                        init() {
                            // Expose global toast() so the Livewire Toast trait can call it
                            window.toast = ({ toast }) => this.add(toast);
                        },

                        add(toast) {
                            const index = this.toasts.length;
                            this.toasts.push({ ...toast, visible: true });

                            setTimeout(() => this.dismiss(index), toast.timeout ?? {{ $timeout }});
                        },

                        dismiss(index) {
                            if (this.toasts[index]) {
                                this.toasts[index].visible = false;
                                setTimeout(() => this.toasts.splice(index, 1), 300);
                            }
                        },
                    };
                }
            </script>
            BLADE;
    }
}
