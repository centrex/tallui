<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Blade;

describe('Button style modifier', function (): void {
    it('defaults to soft when no style class is present', function (): void {
        $html = Blade::render('<x-tallui-button label="Save" class="btn-primary" />');

        expect($html)->toContain('btn-primary')
            ->and($html)->toContain('btn-soft');
    });

    it('does not add btn-soft when the call site already picked a style', function (): void {
        $ghost = Blade::render('<x-tallui-button label="Cancel" class="btn-ghost" />');
        $outline = Blade::render('<x-tallui-button label="Cancel" class="btn-outline btn-primary" />');
        $link = Blade::render('<x-tallui-button label="Cancel" class="btn-link" />');

        expect($ghost)->not->toContain('btn-soft')
            ->and($outline)->not->toContain('btn-soft')
            ->and($link)->not->toContain('btn-soft');
    });

    it('lets a call site force the style explicitly, overriding auto-detection', function (): void {
        $html = Blade::render('<x-tallui-button label="Save" class="btn-primary" style="outline" />');

        expect($html)->toContain('btn-outline')
            ->and($html)->not->toContain('btn-soft');
    });

    it('renders no style modifier when the config default is solid', function (): void {
        config()->set('tallui.style.default', 'solid');

        $html = Blade::render('<x-tallui-button label="Save" class="btn-primary" />');

        expect($html)->toContain('btn-primary')
            ->and($html)->not->toContain('btn-soft')
            ->and($html)->not->toContain('btn-outline')
            ->and($html)->not->toContain('btn-dash');
    });
});
