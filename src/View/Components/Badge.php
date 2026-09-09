<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Centrex\TallUi\Concerns\ResolvesStyleModifier;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Badge extends Component
{
    use ResolvesStyleModifier;

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

    /** Pseudo-colors that already encode their own style — never paired with a style modifier. */
    private const STYLE_LESS_COLORS = ['ghost', 'outline'];

    public function __construct(
        public string $color = 'neutral',
        public ?string $type = null,
        public string $size = '',
        public ?string $style = null,
    ) {}

    public function render(): View|Closure|string
    {
        $resolved = $this->type ?? $this->color;
        $colorClass = self::COLOR_MAP[$resolved] ?? 'badge-neutral';

        $styleClass = in_array($resolved, self::STYLE_LESS_COLORS, true)
            ? null
            : $this->styleModifierClass('badge', $this->resolveStyleModifier($this->style));

        $colorClass = trim($colorClass . ' ' . $styleClass);

        return view('tallui::components.badge')->with(compact('colorClass'));
    }
}
