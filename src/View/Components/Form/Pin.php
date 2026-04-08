<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Centrex\TallUi\Concerns\HasUuid;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Pin extends Component
{
    use HasUuid;

    public function __construct(
        public string $name = '',
        public int $length = 6,
        public ?string $label = null,
        public ?string $error = null,
        public ?string $helper = null,
        public bool $masked = false,     // password dots vs digits
        public bool $numeric = true,     // only allow numbers
        public bool $required = false,
        public string $size = 'md',      // sm | md | lg
        public ?string $id = null,
    ) {
        $this->generateUuid($id);
    }

    public function render(): View|Closure|string
    {
        $inputSizeClass = match ($this->size) {
            'sm'    => 'input-sm w-8 h-8 text-sm',
            'lg'    => 'input-lg w-16 h-16 text-xl',
            default => 'input-md w-12 h-12 text-lg',
        };

        return view('tallui::components.form.pin')->with([
            'inputSizeClass' => $inputSizeClass,
        ]);
    }
}
