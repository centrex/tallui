<?php

declare(strict_types = 1);

use Centrex\TallUi\View\Components\Form\Select;
use Illuminate\Support\Facades\Cache;

describe('Select component properties', function (): void {
    it('defaults to non-searchable', function (): void {
        expect((new Select(name: 'role'))->searchable)->toBeFalse();
    });

    it('accepts options array', function (): void {
        $c = new Select(name: 'role', options: ['admin' => 'Admin', 'user' => 'User']);

        expect($c->options)->toBe(['admin' => 'Admin', 'user' => 'User']);
    });

    it('defaults to empty options', function (): void {
        expect((new Select(name: 'role'))->options)->toBe([]);
    });

    it('accepts searchable prop', function (): void {
        expect((new Select(name: 'role', searchable: true))->searchable)->toBeTrue();
    });

    it('sorts options alphabetically by default', function (): void {
        $c = new Select(name: 'role', options: ['user' => 'User', 'admin' => 'Admin']);

        expect(array_values($c->options))->toBe(['Admin', 'User']);
    });

    it('resolves async select url from searchName', function (): void {
        $c = new Select(name: 'user_id', searchable: true, searchName: 'user');

        expect($c->isAsyncSearch)->toBeTrue()
            ->and($c->resolvedSearchUrl)->toContain('select-search');
    });

    it('resolves async select url from a component-level source definition', function (): void {
        $c = new Select(
            name: 'user_id',
            searchable: true,
            searchSource: [
                'model'          => 'App\\Models\\User',
                'label'          => 'name',
                'value'          => 'id',
                'search_columns' => ['name', 'email'],
            ],
        );

        parse_str((string) parse_url((string) $c->resolvedSearchUrl, PHP_URL_QUERY), $query);

        expect($c->isAsyncSearch)->toBeTrue()
            ->and($query)->toHaveKey('source')
            ->and(Cache::get('tallui:select-source:' . $query['source']))->toBe($c->searchSource);
    });

    it('resolves size class', function (): void {
        expect((new Select(name: 'role', size: 'sm'))->sizeClass)->toBe('select-sm');
    });

    it('normalizes options with sublabels', function (): void {
        $c = new Select(name: 'user_id', options: [
            ['value' => 1, 'label' => 'Alice Smith', 'sublabel' => 'alice@test.com'],
        ]);

        expect($c->normalizedOptions)->toBe([
            ['value' => 1, 'label' => 'Alice Smith', 'sublabel' => 'alice@test.com'],
        ]);
    });
});

describe('Select template content', function (): void {
    it('template contains select-error when error is set', function (): void {
        $c = new Select(name: 'role', error: 'Required');

        expect($c->render())->toContain('select-error');
    });

    it('template renders Alpine.js combobox when searchable', function (): void {
        $c = new Select(name: 'country', searchable: true);

        expect($c->render())->toContain('x-data');
    });

    it('template preserves async selections across searches', function (): void {
        $c = new Select(name: 'customer_id', searchable: true, searchName: 'customer');

        expect($c->render())
            ->toContain('selectedLabel')
            ->toContain('this.allItems = this.mergeItems(this.allItems, normalizedItems);')
            ->toContain('this.allItems = this.mergeItems([item], this.allItems);')
            ->toContain('sublabel');
    });

    it('template positions the searchable dropdown as an overlay', function (): void {
        $c = new Select(name: 'customer_id', searchable: true, searchName: 'customer');

        expect($c->render())
            ->toContain('x-teleport="body"')
            ->toContain('fixed z-[9999] bg-base-100 border border-base-300 rounded-box shadow-lg')
            ->toContain("panelStyle: 'display:none'")
            ->toContain('updatePanelPosition()');
    });

    it('template renders standard select when not searchable', function (): void {
        $c = new Select(name: 'role', searchable: false);

        expect($c->render())->toContain('<select')
            ->and($c->render())->toContain('name="{{ $name }}"');
    });

    it('template renders sublabels for searchable options', function (): void {
        $c = new Select(name: 'customer_id', searchable: true, options: [
            ['value' => 1, 'label' => 'Alice Smith', 'sublabel' => 'alice@test.com'],
        ]);

        expect($c->render())
            ->toContain('x-show="item.sublabel"')
            ->toContain('x-text="item.sublabel"');
    });

    it('template falls back to combined option text for native select options with sublabels', function (): void {
        $c = new Select(name: 'customer_id', options: [
            1 => ['label' => 'Alice Smith', 'sublabel' => 'alice@test.com'],
        ]);

        expect($c->render())
            ->toContain("@if(is_array(\$optionLabel) && array_key_exists('label', \$optionLabel))")
            ->toContain("{{ \$optionLabel['label'] }}")
            ->toContain("{{ \$optionLabel['sublabel'] }}");
    });
});
