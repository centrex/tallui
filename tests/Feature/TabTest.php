<?php

declare(strict_types = 1);

use Centrex\TallUi\View\Components\Tab;
use Illuminate\Support\Facades\Blade;

describe('Tab', function (): void {
    it('defaults active to the first tab id', function (): void {
        $c = new Tab(tabs: [['id' => 'general', 'label' => 'General'], ['id' => 'security', 'label' => 'Security']]);

        expect($c->active)->toBe('general');
    });

    it('respects an explicitly given active tab', function (): void {
        $c = new Tab(tabs: [['id' => 'general', 'label' => 'General'], ['id' => 'security', 'label' => 'Security']], active: 'security');

        expect($c->active)->toBe('security');
    });

    it('no longer permanently hides the default-slot fallback panel', function (): void {
        // Regression: the fallback panel used to be wrapped in x-show="false" (a literal,
        // permanent false), so the documented default-slot usage pattern could never display.
        $c = new Tab(tabs: [['id' => 'general', 'label' => 'General']]);

        expect($c->render())->not->toContain('x-show="false"');
    });

    it('renders the ARIA tabs pattern', function (): void {
        $c = new Tab(tabs: [['id' => 'general', 'label' => 'General']]);
        $rendered = $c->render();

        expect($rendered)->toContain('role="tablist"')
            ->and($rendered)->toContain('role="tab"')
            ->and($rendered)->toContain('aria-selected')
            ->and($rendered)->toContain('aria-controls');
    });

    it('maps variant prop to DaisyUI 5 class names', function (): void {
        $rendered = (new Tab(tabs: [['id' => 'a', 'label' => 'A']], variant: 'lifted'))->render();

        expect($rendered)->toContain("'lifted' => 'tabs-lift'");
    });

    it('renders named-slot panel content when actually compiled', function (): void {
        // Regression: detecting whether named slots were passed used to run
        // isset(${$tab['id']}) inside a closure (collect()->contains(fn...)).
        // Arrow functions only auto-capture variables whose names are
        // statically visible in the function body, so the dynamically-named
        // variable-variable never resolved — named-slot content (the
        // documented primary usage pattern) silently rendered as empty.
        $html = Blade::render(<<<'BLADE'
            <x-tallui-tab :tabs="[['id' => 'general', 'label' => 'General'], ['id' => 'security', 'label' => 'Security']]" active="general">
                <x-slot:general>General settings content</x-slot:general>
                <x-slot:security>Security settings content</x-slot:security>
            </x-tallui-tab>
            BLADE);

        expect($html)->toContain('General settings content')
            ->and($html)->toContain('Security settings content');
    });

    it('renders default-slot fallback content when actually compiled', function (): void {
        $html = Blade::render(<<<'BLADE'
            <x-tallui-tab :tabs="[['id' => 'general', 'label' => 'General']]" active="general">
                <div x-show="activeTab === 'general'">General content</div>
            </x-tallui-tab>
            BLADE);

        expect($html)->toContain('General content');
    });
});
