<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Badge extends Component
{
    private const COLOR_MAP = [
        'success'   => 'badge-success',
        'error'     => 'badge-error',
        'warning'   => 'badge-warning',
        'info'      => 'badge-info',
        'primary'   => 'badge-primary',
        'secondary' => 'badge-secondary',
        'accent'    => 'badge-accent',
        'ghost'     => 'badge-ghost',
        'outline'   => 'badge-outline',
        'neutral'   => 'badge-neutral',
    ];

    public function __construct(
        public string $color = 'neutral',
        public ?string $type = null,
        public string $size = '',
    ) {}

    public function render(): View|Closure|string
    {
        $resolved = $this->type ?? $this->color;
        $colorClass = self::COLOR_MAP[$resolved] ?? 'badge-neutral';

        return view('tallui::components.badge')->with(compact('colorClass'));
    }
}
