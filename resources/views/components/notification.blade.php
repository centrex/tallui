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
    @php
        $hasFlash = session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info') || session()->has('message');
    @endphp

    @if($hasFlash)
        <template x-if="notifications.length === 0">
            <div class="contents" x-data="{ shown: [] }">
                @session('success')
                    <div x-data="{ show: true }" x-show="show && !shown.includes('success')"
                         x-init="setTimeout(() => { shown.push('success'); show = false; }, {{ $timeout }})"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="alert alert-success shadow-lg pointer-events-auto rounded-2xl text-sm" role="alert">
                        {!! $icons['success'] !!}
                        <span>{{ $value }}</span>
                    </div>
                @endsession

                @session('error')
                    <div x-data="{ show: true }" x-show="show && !shown.includes('error')"
                         x-init="setTimeout(() => { shown.push('error'); show = false; }, {{ $timeout }})"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="alert alert-error shadow-lg pointer-events-auto rounded-2xl text-sm" role="alert">
                        {!! $icons['error'] !!}
                        <span>{{ $value }}</span>
                    </div>
                @endsession

                @session('warning')
                    <div x-data="{ show: true }" x-show="show && !shown.includes('warning')"
                         x-init="setTimeout(() => { shown.push('warning'); show = false; }, {{ $timeout }})"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="alert alert-warning shadow-lg pointer-events-auto rounded-2xl text-sm" role="alert">
                        {!! $icons['warning'] !!}
                        <span>{{ $value }}</span>
                    </div>
                @endsession

                @session('info')
                    <div x-data="{ show: true }" x-show="show && !shown.includes('info')"
                         x-init="setTimeout(() => { shown.push('info'); show = false; }, {{ $timeout }})"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="alert alert-info shadow-lg pointer-events-auto rounded-2xl text-sm" role="alert">
                        {!! $icons['info'] !!}
                        <span>{{ $value }}</span>
                    </div>
                @endsession

                @session('message')
                    <div x-data="{ show: true }" x-show="show && !shown.includes('message')"
                         x-init="setTimeout(() => { shown.push('message'); show = false; }, {{ $timeout }})"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="alert alert-info shadow-lg pointer-events-auto rounded-2xl text-sm" role="alert">
                        {!! $icons['info'] !!}
                        <span>{{ $value }}</span>
                    </div>
                @endsession
            </div>
        </template>
    @endif

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
            <template x-if="n.type === 'success'">{!! $icons['success'] !!}</template>
            <template x-if="n.type === 'error'">{!! $icons['error'] !!}</template>
            <template x-if="n.type === 'warning'">{!! $icons['warning'] !!}</template>
            <template x-if="n.type === 'info'">{!! $icons['info'] !!}</template>
            <span x-text="n.message"></span>
            {!! $closeBtn !!}
        </div>
    </template>
</div>
