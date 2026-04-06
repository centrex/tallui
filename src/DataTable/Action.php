<?php

declare(strict_types = 1);

namespace Centrex\TallUi\DataTable;

class Action
{
    public ?string $icon          = null;
    public ?string $route         = null;
    public ?string $routeKey      = 'id';
    public ?string $emitEvent     = null;
    public ?string $emitKey       = 'id';
    public ?string $confirmMessage = null;
    public string $color          = 'ghost';

    public function __construct(
        public readonly string $label,
    ) {}

    public static function make(string $label): static
    {
        return new static($label);
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function route(string $route, string $key = 'id'): static
    {
        $this->route    = $route;
        $this->routeKey = $key;

        return $this;
    }

    public function emit(string $event, string $key = 'id'): static
    {
        $this->emitEvent = $event;
        $this->emitKey   = $key;

        return $this;
    }

    public function confirm(string $message = 'Are you sure?'): static
    {
        $this->confirmMessage = $message;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label'          => $this->label,
            'icon'           => $this->icon,
            'route'          => $this->route,
            'routeKey'       => $this->routeKey,
            'emitEvent'      => $this->emitEvent,
            'emitKey'        => $this->emitKey,
            'confirmMessage' => $this->confirmMessage,
            'color'          => $this->color,
        ];
    }
}
