<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Notification extends Component
{
    public function __construct(
        public int $timeout = 4000,
        public string $position = 'top-right',
    ) {}

    public function render(): View|Closure|string
    {
        return view('tallui::components.notification');
    }
}
