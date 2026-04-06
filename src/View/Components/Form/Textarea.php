<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public string $sizeClass;

    public function __construct(
        public string $name = '',
        public ?string $label = null,
        public ?string $placeholder = null,
        public ?string $helper = null,
        public ?string $error = null,
        public int $rows = 4,
        public bool $required = false,
        public bool $disabled = false,
        public string $size = '',
    ) {
        $configSize = config('tallui.forms.size', 'md');
        $resolved = $size ?: $configSize;

        $this->sizeClass = match ($resolved) {
            'xs'    => 'textarea-xs',
            'sm'    => 'textarea-sm',
            'lg'    => 'textarea-lg',
            default => 'textarea-md',
        };
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class(['form-control w-full', 'opacity-60' => $disabled])>
                @if($label)
                    <label @if($name) for="{{ $name }}" @endif class="label">
                        <span class="label-text font-medium">
                            {{ $label }}
                            @if($required) <span class="text-error ml-0.5">*</span> @endif
                        </span>
                    </label>
                @endif

                <textarea
                    id="{{ $name }}"
                    name="{{ $name }}"
                    rows="{{ $rows }}"
                    @if($placeholder) placeholder="{{ $placeholder }}" @endif
                    @if($required) required @endif
                    @if($disabled) disabled @endif
                    {{ $attributes->class([
                        'textarea textarea-bordered w-full',
                        $sizeClass,
                        'textarea-error' => $error,
                    ])->merge() }}
                >{{ $slot }}</textarea>

                @if($error)
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $error }}</span>
                    </label>
                @elseif($helper)
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">{{ $helper }}</span>
                    </label>
                @endif
            </div>
            BLADE;
    }
}
