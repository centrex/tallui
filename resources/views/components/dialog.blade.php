<div
    x-data="{ open: false }"
    @open-dialog.window="if ($event.detail === '{{ $id }}') open = true"
    @close-dialog.window="if ($event.detail === '{{ $id }}') open = false"
    @keydown.escape.window="if (open && {{ $closeable ? 'true' : 'false' }}) open = false"
>
    {{-- Optional inline trigger --}}
    @if(isset($trigger))
        <span @click="open = true">{{ $trigger }}</span>
    @endif

    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9990] bg-black/40"
        @if($closeable) @click="open = false" @endif
        style="display:none"
    ></div>

    {{-- Dialog panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 z-[9991] flex items-center justify-center p-4"
        style="display:none"
    >
        <div
            @class([
                'bg-base-100 rounded-2xl shadow-2xl w-full flex flex-col text-center',
                'max-w-xs'  => $size === 'sm',
                'max-w-sm'  => $size === 'md',
                'max-w-lg'  => $size === 'lg',
            ])
            @click.stop
        >
            {{-- Icon + Title --}}
            <div class="px-6 pt-7 pb-4 flex flex-col items-center gap-3">
                @if($icon)
                    <span class="{{ $iconColor }}">
                        <x-tallui-icon :name="$icon" class="w-12 h-12" />
                    </span>
                @endif
                @if($title)
                    <h3 class="font-bold text-xl">{{ $title }}</h3>
                @endif
            </div>

            {{-- Body --}}
            <div class="px-6 pb-5 text-sm text-base-content/70">
                {{ $slot }}
            </div>

            {{-- Footer / actions --}}
            @if(isset($footer))
                <div class="flex items-center justify-center gap-3 px-6 py-4 border-t border-base-200">
                    {{ $footer }}
                </div>
            @endif

            {{-- Default close button if no footer --}}
            @if(!isset($footer) && $closeable)
                <div class="px-6 pb-5">
                    <button @click="open = false" class="btn btn-block btn-ghost">
                        {{ __('Close') }}
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
