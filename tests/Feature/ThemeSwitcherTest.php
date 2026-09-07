<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Blade;

it('renders every configured theme option and the theme-change plumbing', function (): void {
    $html = Blade::render('<x-tallui-theme-switcher />');

    expect($html)
        ->toContain("document.documentElement.setAttribute('data-theme', this.theme)")
        ->toContain("localStorage.setItem('theme-mode'")
        ->toContain("window.dispatchEvent(new CustomEvent('tallui-theme-changed'");

    foreach (config('tallui.theme.options') as $option) {
        expect($html)->toContain($option['name']);
    }
});

it('accepts a custom theme list and default instead of the config one', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-tallui-theme-switcher
            :themes="[['name' => 'acid', 'label' => 'Acid', 'mode' => 'light']]"
            default="acid"
        />
        BLADE);

    expect($html)
        ->toContain('acid')
        ->not->toContain('dracula');
});
