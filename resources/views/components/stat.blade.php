<div class="stat" {{ $attributes }}>
    @if($icon)
        <div class="stat-figure {{ $iconColor }}">
            <x-tallui-icon :name="$icon" class="w-8 h-8" />
        </div>
    @elseif($slot->isNotEmpty())
        <div class="stat-figure">{{ $slot }}</div>
    @endif

    <div class="stat-title">{{ $title }}</div>
    <div class="stat-value">{{ $value }}</div>

    @if($desc || $change)
        <div class="stat-desc flex items-center gap-1">
            @if($change)
                @if($changeType === 'up')
                    <x-tallui-icon name="o-arrow-trending-up" class="w-3 h-3 text-success shrink-0" />
                    <span class="text-success">{{ $change }}</span>
                @elseif($changeType === 'down')
                    <x-tallui-icon name="o-arrow-trending-down" class="w-3 h-3 text-error shrink-0" />
                    <span class="text-error">{{ $change }}</span>
                @else
                    <span>{{ $change }}</span>
                @endif
            @endif
            @if($desc)
                <span>{{ $desc }}</span>
            @endif
        </div>
    @endif
</div>
