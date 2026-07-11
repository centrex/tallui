<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Blade;

describe('Button confirm helper', function (): void {
    it('does not render a confirm dialog when confirm is not set', function (): void {
        $html = Blade::render('<x-tallui-button label="Save" wire:click="save" />');

        expect($html)->not->toContain('role="alertdialog"');
    });

    it('renders a confirm dialog wired to the button when confirm is set', function (): void {
        $html = Blade::render('<x-tallui-button label="Delete" wire:click="delete" confirm="This cannot be undone." />');

        expect($html)->toContain('role="alertdialog"')
            ->and($html)->toContain('This cannot be undone.')
            ->and($html)->toContain('tallui-confirm-open')
            ->and($html)->toContain('tallui-confirm-yes');
    });

    it('keeps the original wire:click attribute intact on the trigger element', function (): void {
        $html = Blade::render('<x-tallui-button label="Delete" wire:click="delete(5)" confirm="Sure?" />');

        expect($html)->toContain('wire:click="delete(5)"');
    });
});
