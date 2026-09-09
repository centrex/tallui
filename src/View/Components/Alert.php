<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Centrex\TallUi\Concerns\ResolvesStyleModifier;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    use ResolvesStyleModifier;

    private const ALERT_CLASS_MAP = [
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'error'   => 'alert-error',
        'info'    => 'alert-info',
    ];

    private const DEFAULT_ICONS = [
        'success' => 'o-check-circle',
        'warning' => 'o-exclamation-triangle',
        'error'   => 'o-x-circle',
        'info'    => 'o-information-circle',
    ];

    public function __construct(
        public string $type = 'info',
        public ?string $title = null,
        public ?string $icon = null,
        public bool $dismissible = false,
        public ?string $style = null,
    ) {}

    public function render(): View|Closure|string
    {
        $colorClass = self::ALERT_CLASS_MAP[$this->type] ?? 'alert-info';
        $styleClass = $this->styleModifierClass('alert', $this->resolveStyleModifier($this->style));

        return view('tallui::components.alert')->with([
            'alertClass' => trim($colorClass . ' ' . $styleClass),
            'icon'       => $this->icon ?? self::DEFAULT_ICONS[$this->type] ?? 'o-information-circle',
        ]);
    }
}
