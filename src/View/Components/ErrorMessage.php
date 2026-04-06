<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ErrorMessage extends Component
{
    public function __construct(
        public ?string $message = null,
        public string  $field   = '',
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            @if($message || ($field && $errors->has($field)))
                <p class="text-error text-sm mt-1" {{ $attributes }}>
                    {{ $message ?? $errors->first($field) }}
                </p>
            @endif
            BLADE;
    }
}
