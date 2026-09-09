<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Blade;

describe('Alert style modifier', function (): void {
    it('defaults to soft', function (): void {
        $html = Blade::render('<x-tallui-alert type="warning">Careful</x-tallui-alert>');

        expect($html)->toContain('alert-warning')
            ->and($html)->toContain('alert-soft');
    });

    it('lets a call site override the style explicitly', function (): void {
        $html = Blade::render('<x-tallui-alert type="warning" style="dash">Careful</x-tallui-alert>');

        expect($html)->toContain('alert-warning')
            ->and($html)->toContain('alert-dash')
            ->and($html)->not->toContain('alert-soft');
    });

    it('renders no style modifier when the config default is solid', function (): void {
        config()->set('tallui.style.default', 'solid');

        $html = Blade::render('<x-tallui-alert type="warning">Careful</x-tallui-alert>');

        expect($html)->toContain('alert-warning')
            ->and($html)->not->toContain('alert-soft');
    });
});
