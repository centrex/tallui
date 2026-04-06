<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Checkbox extends Component
{
    public function __construct(
        public string $name = '',
        public ?string $label = null,
        public ?string $helper = null,
        public ?string $error = null,
        public bool $checked = false,
        public bool $disabled = false,
        public string $color = '',
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div @class(['form-control', 'opacity-60' => $disabled])>
                <label class="label cursor-pointer justify-start gap-3">
                    <input
                        type="checkbox"
                        id="{{ $name }}"
                        name="{{ $name }}"
                        @if($checked) checked @endif
                        @if($disabled) disabled @endif
                        {{ $attributes->class([
                            'checkbox',
                            "checkbox-{$color}" => $color,
                            'checkbox-error' => $error,
                        ])->merge() }}
                    />
                    @if($label)
                        <span class="label-text">{{ $label }}</span>
                    @endif
                </label>

                @if($error)
                    <label class="label pt-0">
                        <span class="label-text-alt text-error">{{ $error }}</span>
                    </label>
                @elseif($helper)
                    <label class="label pt-0">
                        <span class="label-text-alt text-base-content/60">{{ $helper }}</span>
                    </label>
                @endif
            </div>
            BLADE;
    }
}
