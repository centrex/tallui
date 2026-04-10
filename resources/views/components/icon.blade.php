<span
    @if($id) id="{{ $id }}" @endif
    @if($label) aria-label="{{ $label }}" title="{{ $label }}" @endif
    class="inline-flex items-center gap-1"
>
    <x-svg :name="$resolvedName" {{ $attributes->class([$size => !$attributes->has('class')]) }} />
    @if($label)
        <span class="text-sm">{{ $label }}</span>
    @endif
</span>
