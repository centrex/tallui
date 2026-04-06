<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class File extends Component
{
    public string $sizeClass;

    public function __construct(
        public string $name = '',
        public ?string $label = null,
        public ?string $helper = null,
        public ?string $error = null,
        public bool $multiple = false,
        public ?string $accept = null,
        public string $size = '',
        public bool $bordered = true,
        public bool $required = false,
    ) {
        $this->sizeClass = match ($size ?: config('tallui.forms.size', 'md')) {
            'xs'    => 'file-input-xs',
            'sm'    => 'file-input-sm',
            'lg'    => 'file-input-lg',
            default => 'file-input-md',
        };
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div class="form-control w-full">
                @if($label)
                    <label @if($name) for="{{ $name }}" @endif class="label">
                        <span class="label-text font-medium">
                            {{ $label }}
                            @if($required) <span class="text-error ml-0.5">*</span> @endif
                        </span>
                    </label>
                @endif

                <input
                    type="file"
                    id="{{ $name }}"
                    name="{{ $name }}{{ $multiple ? '[]' : '' }}"
                    @if($multiple) multiple @endif
                    @if($accept) accept="{{ $accept }}" @endif
                    @if($required) required @endif
                    {{ $attributes->class([
                        'file-input w-full',
                        'file-input-bordered' => $bordered,
                        $sizeClass,
                        'file-input-error' => $error,
                    ]) }}
                />

                @if($error)
                    <label class="label"><span class="label-text-alt text-error">{{ $error }}</span></label>
                @elseif($helper)
                    <label class="label"><span class="label-text-alt text-base-content/60">{{ $helper }}</span></label>
                @endif
            </div>
            BLADE;
    }
}
