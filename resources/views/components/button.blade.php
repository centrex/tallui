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

    @if($spinner)
        wire:target="{{ $spinnerTarget }}"
        wire:loading.attr="disabled"
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
