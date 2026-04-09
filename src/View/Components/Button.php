<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Centrex\TallUi\Concerns\HasUuid;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    use HasUuid;

    public function __construct(
        public ?string $id = null,
        public ?string $label = null,
        public ?string $icon = null,
        public ?string $iconRight = null,
        public ?string $spinner = null,
        public ?string $link = null,
        public ?bool $external = false,
        public ?bool $noWireNavigate = false,
        public ?bool $responsive = false,
        public ?string $badge = null,
        public ?string $badgeClasses = null,
        public ?string $tooltip = null,
        public ?string $tooltipLeft = null,
        public ?string $tooltipRight = null,
        public ?string $tooltipBottom = null,
    ) {
        $this->tooltip ??= $this->tooltipLeft ?? $this->tooltipRight ?? $this->tooltipBottom;
        $this->generateUuid($id);
    }

    public function render(): View|Closure|string
    {
        $tooltipPosition = $this->tooltipLeft
            ? 'lg:tooltip-left'
            : ($this->tooltipRight ? 'lg:tooltip-right' : ($this->tooltipBottom ? 'lg:tooltip-bottom' : 'lg:tooltip-top'));

        $spinnerTarget = null;

        if ($this->spinner !== null) {
            $spinnerTarget = $this->spinner === '1'
                ? $this->attributes->whereStartsWith('wire:click')->first() ?? 'defaultAction'
                : $this->spinner;
        }

        return view('tallui::components.button')->with(compact('tooltipPosition', 'spinnerTarget'));
    }
}
