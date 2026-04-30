<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PasswordInput extends Component
{
    public string $sizeClass;

    public function __construct(
        public string $name = 'password',
        public ?string $label = null,
        public ?string $placeholder = null,
        public ?string $helper = null,
        public ?string $error = null,
        public ?string $icon = null,
        public bool $showToggle = true,
        public bool $required = false,
        public bool $disabled = false,
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
            <div
                @class(['form-control w-full', 'opacity-60' => $disabled])
                x-data="{ show: false }"
            >
                @if($label)
                    <label @if($name) for="{{ $name }}" @endif class="label">
                        <span class="label-text font-medium">
                            {{ $label }}
                            @if($required) <span class="text-error ml-0.5">*</span> @endif
                        </span>
                    </label>
                @endif

                <div @class(['relative flex items-center input input-bordered', $sizeClass, 'input-error' => $error])>
                    @if($icon)
                        <span class="mr-2 text-base-content/50">
                            <x-tallui-icon :name="$icon" class="w-4 h-4" />
                        </span>
                    @endif

                    <input
                        :type="show ? 'text' : 'password'"
                        id="{{ $name }}"
                        name="{{ $name }}"
                        @if($placeholder) placeholder="{{ $placeholder }}" @endif
                        @if($required) required @endif
                        @if($disabled) disabled @endif
                        {{ $attributes->class([
                            'grow bg-transparent outline-none border-none p-0 focus:ring-0',
                        ])->merge() }}
                    />

                    @if($showToggle)
                        <button
                            type="button"
                            @click="show = !show"
                            class="ml-2 text-base-content/50 hover:text-base-content focus:outline-none"
                            :aria-label="show ? 'Hide password' : 'Show password'"
                        >
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    @endif
                </div>

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
