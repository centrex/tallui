<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public string $id = 'modal',
        public string $title = '',
        public string $size = 'md',   // sm | md | lg | xl | full
        public bool $closeable = true,   // show X button + close on backdrop click
        public ?string $icon = null,   // Heroicon in the title bar
        public string $iconColor = 'text-primary',
    ) {}

    public function render(): View|Closure|string
    {
        return view('tallui::components.modal');
    }
}
