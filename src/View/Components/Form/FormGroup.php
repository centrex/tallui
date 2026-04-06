<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormGroup extends Component
{
    public function __construct(
        public ?string $label = null,
        public ?string $for = null,
        public ?string $helper = null,
        public ?string $error = null,
        public bool $required = false,
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div class="form-control w-full">
                @if($label)
                    <label @if($for) for="{{ $for }}" @endif class="label">
                        <span class="label-text font-medium">
                            {{ $label }}
                            @if($required)
                                <span class="text-error ml-0.5">*</span>
                            @endif
                        </span>
                    </label>
                @endif

                {{ $slot }}

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
