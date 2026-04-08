<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    private const ALERT_CLASS_MAP = [
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'error'   => 'alert-error',
        'info'    => 'alert-info',
    ];

    private const DEFAULT_ICONS = [
        'success' => 'heroicon-o-check-circle',
        'warning' => 'heroicon-o-exclamation-triangle',
        'error'   => 'heroicon-o-x-circle',
        'info'    => 'heroicon-o-information-circle',
    ];

    public function __construct(
        public string $type = 'info',
        public ?string $title = null,
        public ?string $icon = null,
        public bool $dismissible = false,
    ) {}

    public function render(): View|Closure|string
    {
        return view('tallui::components.alert')->with([
            'alertClass' => self::ALERT_CLASS_MAP[$this->type] ?? 'alert-info',
            'icon'       => $this->icon ?? self::DEFAULT_ICONS[$this->type] ?? 'heroicon-o-information-circle',
        ]);
    }
}
