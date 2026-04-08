<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Centrex\TallUi\Concerns\HasUuid;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dialog extends Component
{
    use HasUuid;

    public function __construct(
        public ?string $id = null,
        public string $title = '',
        public string $type = 'info',      // info | success | warning | error | confirm
        public ?string $icon = null,
        public bool $closeable = true,
        public string $size = 'sm',        // sm | md | lg
    ) {
        $this->generateUuid($id);

        // Auto-assign icon per type if not provided
        $this->icon ??= match ($type) {
            'success' => 'heroicon-o-check-circle',
            'warning' => 'heroicon-o-exclamation-triangle',
            'error'   => 'heroicon-o-x-circle',
            'confirm' => 'heroicon-o-question-mark-circle',
            default   => 'heroicon-o-information-circle',
        };
    }

    public function render(): View|Closure|string
    {
        $iconColorMap = [
            'success' => 'text-success',
            'warning' => 'text-warning',
            'error'   => 'text-error',
            'confirm' => 'text-primary',
            'info'    => 'text-info',
        ];

        $iconColor = $iconColorMap[$this->type] ?? 'text-primary';

        return view('tallui::components.dialog')->with(compact('iconColor'));
    }
}
