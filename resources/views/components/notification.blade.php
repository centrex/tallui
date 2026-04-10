@php
    $positionClass = match($position) {
        'top-left'     => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left'  => 'bottom-4 left-4',
        'top-center'   => 'top-4 left-1/2 -translate-x-1/2',
        default        => 'top-4 right-4',
    };
@endphp

<div
    x-data="{
        notifications: [],
        add(type, message, timeout) {
            const id = Date.now();
            this.notifications.push({ id, type, message, visible: true });
            if (timeout > 0) setTimeout(() => this.remove(id), timeout);
        },
        remove(id) {
            const n = this.notifications.find(n => n.id === id);
            if (n) n.visible = false;
            setTimeout(() => { this.notifications = this.notifications.filter(n => n.id !== id); }, 300);
        }
    }"
    @notify.window="add($event.detail.type ?? 'info', $event.detail.message, {{ $timeout }})"
    class="fixed z-[9999] flex flex-col gap-2 w-full max-w-sm pointer-events-none {{ $positionClass }}"
    {{ $attributes }}
>
    {{-- ── Session flash toasts (server-rendered) ─────────────────── --}}
    @foreach(['success' => 'alert-success', 'error' => 'alert-error', 'warning' => 'alert-warning', 'info' => 'alert-info', 'message' => 'alert-info'] as $key => $cls)
        @if(session()->has($key))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, {{ $timeout }})"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="alert {{ $cls }} shadow-lg pointer-events-auto rounded-2xl text-sm"
                role="alert"
            >
                @if($key === 'success' || ($key === 'message' && session('message')))
                    <x-tallui-icon name="o-check-circle" class="w-5 h-5 shrink-0" />
                @elseif($key === 'error')
                    <x-tallui-icon name="o-x-circle" class="w-5 h-5 shrink-0" />
                @elseif($key === 'warning')
                    <x-tallui-icon name="o-exclamation-triangle" class="w-5 h-5 shrink-0" />
                @else
                    <x-tallui-icon name="o-information-circle" class="w-5 h-5 shrink-0" />
                @endif
                <span>{{ session($key) }}</span>
                <button type="button" @click="show = false" class="btn btn-ghost btn-xs btn-circle ml-auto">
                    <x-tallui-icon name="o-x-mark" class="w-3 h-3" />
                </button>
            </div>
        @endif
    @endforeach

    {{-- ── Dynamic toasts via @notify event (Alpine x-for) ────────── --}}
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
                'alert-error':   n.type === 'error',
                'alert-warning': n.type === 'warning',
                'alert-info':    n.type === 'info' || n.type === 'message',
            }"
            class="alert shadow-lg pointer-events-auto rounded-2xl text-sm"
            role="alert"
        >
            {{-- Type icons — each wrapped in x-if so Alpine controls visibility --}}
            <template x-if="n.type === 'success'">
                <x-tallui-icon name="o-check-circle" class="w-5 h-5 shrink-0" />
            </template>
            <template x-if="n.type === 'error'">
                <x-tallui-icon name="o-x-circle" class="w-5 h-5 shrink-0" />
            </template>
            <template x-if="n.type === 'warning'">
                <x-tallui-icon name="o-exclamation-triangle" class="w-5 h-5 shrink-0" />
            </template>
            <template x-if="n.type === 'info' || n.type === 'message'">
                <x-tallui-icon name="o-information-circle" class="w-5 h-5 shrink-0" />
            </template>

            <span x-text="n.message" class="flex-1 min-w-0"></span>

            <button type="button" @click="remove(n.id)" class="btn btn-ghost btn-xs btn-circle ml-auto shrink-0">
                <x-tallui-icon name="o-x-mark" class="w-3 h-3" />
            </button>
        </div>
    </template>
</div>
