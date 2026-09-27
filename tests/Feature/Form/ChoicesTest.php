<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Js;

it('uses its static selection when no wire:model is given', function (): void {
    $html = Blade::render('<x-tallui-choices name="tags" :options="[1 => \'One\', 2 => \'Two\']" :selected="[\'2\']" />');

    expect($html)
        ->toContain('selected: ' . Js::from(['2']))
        ->not->toContain('$wire.entangle');
});

it('entangles the selection with the wire:model property', function (): void {
    $html = Blade::render('<x-tallui-choices name="brandIds" wire:model.live="brandIds" :options="[1 => \'One\']" />');

    expect($html)->toContain('selected: $wire.entangle(\'brandIds\').live');
});

it('entangles without .live for a deferred wire:model', function (): void {
    $html = Blade::render('<x-tallui-choices name="brandIds" wire:model="brandIds" :options="[1 => \'One\']" />');

    expect($html)
        ->toContain('selected: $wire.entangle(\'brandIds\')')
        ->not->toContain("entangle('brandIds').live");
});
