<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Blade;

describe('Badge style modifier', function (): void {
    it('defaults to soft for a colored badge', function (): void {
        $html = Blade::render('<x-tallui-badge type="success">Active</x-tallui-badge>');

        expect($html)->toContain('badge-success')
            ->and($html)->toContain('badge-soft');
    });

    it('does not add a style modifier to the ghost/outline pseudo-colors', function (): void {
        $ghost = Blade::render('<x-tallui-badge color="ghost">Draft</x-tallui-badge>');
        $outline = Blade::render('<x-tallui-badge color="outline">Draft</x-tallui-badge>');

        expect($ghost)->not->toContain('badge-soft')
            ->and($outline)->not->toContain('badge-soft');
    });

    it('lets a call site override the style explicitly', function (): void {
        $html = Blade::render('<x-tallui-badge type="success" style="outline">Active</x-tallui-badge>');

        expect($html)->toContain('badge-success')
            ->and($html)->toContain('badge-outline')
            ->and($html)->not->toContain('badge-soft');
    });

    it('renders no style modifier when the config default is solid', function (): void {
        config()->set('tallui.style.default', 'solid');

        $html = Blade::render('<x-tallui-badge type="success">Active</x-tallui-badge>');

        expect($html)->toContain('badge-success')
            ->and($html)->not->toContain('badge-soft')
            ->and($html)->not->toContain('badge-outline')
            ->and($html)->not->toContain('badge-dash');
    });
});
