<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icon extends Component
{
    public string $resolvedName;

    public function __construct(
        public string $name,
        public ?string $id = null,
        public ?string $label = null,
        public string $size = 'w-5 h-5',
    ) {
        $prefix = config('blade-heroicons.prefix', 'heroicon');

        $this->resolvedName = str_starts_with($this->name, $prefix . '-')
            ? $this->name
            : $prefix . '-' . $this->name;
    }

    public function render(): View|Closure|string
    {
        return view('tallui::components.icon');
    }
}
