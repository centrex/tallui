<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DatePicker extends Component
{
    public string $sizeClass;

    public function __construct(
        public string $name = '',
        public ?string $label = null,
        public ?string $placeholder = null,
        public ?string $helper = null,
        public ?string $error = null,
        public ?string $min = null,
        public ?string $max = null,
        public bool $required = false,
        public bool $disabled = false,
        public bool $withTime = false,
        public string $size = '',
    ) {
        $configSize = config('tallui.forms.size', 'md');
        $resolved = $size ?: $configSize;

        $this->sizeClass = match ($resolved) {
            'xs'    => 'input-xs',
            'sm'    => 'input-sm',
            'lg'    => 'input-lg',
            default => 'input-md',
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

                <input
                    type="{{ $withTime ? 'datetime-local' : 'date' }}"
                    id="{{ $name }}"
                    name="{{ $name }}"
                    @if($placeholder) placeholder="{{ $placeholder }}" @endif
                    @if($min) min="{{ $min }}" @endif
                    @if($max) max="{{ $max }}" @endif
                    @if($required) required @endif
                    @if($disabled) disabled @endif
                    {{ $attributes->class([
                        'input input-bordered w-full',
                        $sizeClass,
                        'input-error' => $error,
                    ])->merge() }}
                />

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
