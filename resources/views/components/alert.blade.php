<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    role="alert"
    {{ $attributes->class(['alert', $alertClass, 'shadow-sm rounded-2xl']) }}
>
    <x-tallui-icon :name="$icon" class="w-5 h-5 shrink-0" />

    <div class="flex-1 min-w-0">
        @if($title)
            <div class="font-semibold text-sm">{{ $title }}</div>
        @endif
        @if($slot->isNotEmpty())
            <div @class(['text-sm' => !$title, 'text-xs opacity-80' => $title])>{{ $slot }}</div>
        @endif
    </div>

    @if($dismissible)
        <button
            type="button"
            @click="show = false"
            class="btn btn-ghost btn-xs btn-circle opacity-60 hover:opacity-100 shrink-0"
            aria-label="Dismiss"
        >
            <x-tallui-icon name="o-x-mark" class="w-4 h-4" />
        </button>
    @endif
</div>
