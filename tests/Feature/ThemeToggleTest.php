<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Blade;

it('syncs both daisyui theme and tailwind dark mode hooks', function (): void {
    $html = Blade::render('<x-tallui-theme-toggle />');

    expect($html)
        ->toContain("localStorage.setItem('theme-mode', this.mode)")
        ->toContain("document.documentElement.classList.toggle('dark', this.mode === 'dark')")
        ->toContain("localStorage.getItem('theme-mode')");
});
