<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Stat extends Component
{
    public function __construct(
        public string $title = '',
        public string $value = '',
        public ?string $desc = null,
        public ?string $icon = null,
        public string $iconColor = 'text-primary',
        public ?string $change = null,
        public string $changeType = 'neutral',  // up | down | neutral
    ) {}

    public function render(): View|Closure|string
    {
        return view('tallui::components.stat');
    }
}
