@if($link)
    <a href="{!! $link !!}"
@else
    <button
@endif

    wire:key="{{ $uuid }}"
    {{ $attributes->whereDoesntStartWith('class')->merge(['type' => 'button']) }}
    {{ $attributes->class(['btn', "!inline-flex lg:tooltip $tooltipPosition" => $tooltip]) }}

    @if($link && $external)
        target="_blank"
    @endif

    @if($link && !$external && !$noWireNavigate)
        wire:navigate
    @endif

    @if($tooltip)
        data-tip="{{ $tooltip }}"
    @endif

    @if($spinner && $spinnerTarget)
        wire:target="{{ $spinnerTarget }}"
        wire:loading.attr="disabled"
    @endif

    @if($confirm)
        x-data="{ tallUiConfirmed: false }"
        @tallui-confirm-yes.window="if ($event.detail.id === '{{ $uuid }}') { tallUiConfirmed = true; $nextTick(() => $el.click()); }"
        @click="if (!tallUiConfirmed) { $event.preventDefault(); $event.stopImmediatePropagation(); window.dispatchEvent(new CustomEvent('tallui-confirm-open', { detail: { id: '{{ $uuid }}' } })); } else { tallUiConfirmed = false; }"
    @endif
>

    @if($spinner && !$iconRight)
        <span wire:loading wire:target="{{ $spinnerTarget }}" class="loading loading-spinner w-5 h-5"></span>
    @endif

    @if($icon)
        <span class="block" @if($spinner) wire:loading.class="hidden" wire:target="{{ $spinnerTarget }}" @endif>
            <x-tallui-icon :name="$icon" />
        </span>
    @endif

    @if($label)
        <span @class(["hidden lg:block" => $responsive ])>
            {{ $label }}
        </span>
        @if(strlen($badge ?? '') > 0)
            <span class="badge badge-sm {{ $badgeClasses }}">{{ $badge }}</span>
        @endif
    @else
        {{ $slot }}
    @endif

    @if($iconRight)
        <span class="block" @if($spinner) wire:loading.class="hidden" wire:target="{{ $spinnerTarget }}" @endif>
            <x-tallui-icon :name="$iconRight" />
        </span>
    @endif

    @if($spinner && $iconRight)
        <span wire:loading wire:target="{{ $spinnerTarget }}" class="loading loading-spinner w-5 h-5"></span>
    @endif

@if(!$link)
    </button>
@else
    </a>
@endif

@if($confirm)
    @php
        $confirmIconColorMap = [
            'warning' => 'text-warning',
            'error'   => 'text-error',
            'confirm' => 'text-primary',
        ];
        $confirmIcon = match ($confirmType) {
            'warning' => 'o-exclamation-triangle',
            'error'   => 'o-x-circle',
            default   => 'o-question-mark-circle',
        };
    @endphp
    <div
        x-data="{
            tallUiConfirmOpen: false,
            returnFocusEl: null,
            focusables() {
                return [...(this.$refs.confirmPanel?.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex=\'-1\'])') ?? [])]
                    .filter((el) => !el.disabled && el.offsetParent !== null);
            },
        }"
        @tallui-confirm-open.window="if ($event.detail.id === '{{ $uuid }}') { tallUiConfirmOpen = true; returnFocusEl = document.activeElement; $nextTick(() => focusables()[0]?.focus()); }"
        x-effect="if (!tallUiConfirmOpen && returnFocusEl) { returnFocusEl.focus?.(); returnFocusEl = null; }"
        @keydown.escape.window="tallUiConfirmOpen = false"
    >
        <template x-teleport="body">
            <div x-show="tallUiConfirmOpen" x-cloak style="display:none">
                <div
                    class="fixed inset-0 z-[9990] bg-black/40"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    @click="tallUiConfirmOpen = false"
                ></div>
                <div class="fixed inset-0 z-[9991] flex items-center justify-center p-4" @click.self="tallUiConfirmOpen = false">
                    <div
                        x-ref="confirmPanel"
                        role="alertdialog"
                        aria-modal="true"
                        aria-labelledby="{{ $uuid }}-confirm-title"
                        @keydown.tab="const items = focusables(); if (!items.length) return; const first = items[0]; const last = items[items.length - 1]; if ($event.shiftKey && document.activeElement === first) { $event.preventDefault(); last.focus(); } else if (!$event.shiftKey && document.activeElement === last) { $event.preventDefault(); first.focus(); }"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="bg-base-100 rounded-2xl shadow-2xl w-full max-w-xs flex flex-col text-center"
                        @click.stop
                    >
                        <div class="px-6 pt-7 pb-4 flex flex-col items-center gap-3">
                            <span class="{{ $confirmIconColorMap[$confirmType] ?? 'text-primary' }}">
                                <x-tallui-icon :name="$confirmIcon" class="w-12 h-12" />
                            </span>
                            <h3 id="{{ $uuid }}-confirm-title" class="font-bold text-xl">{{ $confirmTitle }}</h3>
                        </div>
                        <div class="px-6 pb-5 text-sm text-base-content/70">{{ $confirm }}</div>
                        <div class="flex items-center justify-center gap-3 px-6 pb-6">
                            <button type="button" @click="tallUiConfirmOpen = false" class="btn btn-ghost flex-1">{{ $cancelLabel }}</button>
                            <button
                                type="button"
                                @class(['btn flex-1', 'btn-error' => $confirmType === 'error', 'btn-warning' => $confirmType === 'warning', 'btn-primary' => $confirmType === 'confirm'])
                                @click="tallUiConfirmOpen = false; window.dispatchEvent(new CustomEvent('tallui-confirm-yes', { detail: { id: '{{ $uuid }}' } }))"
                            >{{ $confirmLabel }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endif
