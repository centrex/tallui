<?php

declare(strict_types = 1);

use Centrex\TallUi\View\Components\Form\Select;

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

    it('resolves size class', function (): void {
        expect((new Select(name: 'role', size: 'sm'))->sizeClass)->toBe('select-sm');
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

    it('template renders standard select when not searchable', function (): void {
        $c = new Select(name: 'role', searchable: false);

        // Should have <select> tag, not Alpine combobox
        expect($c->render())->toContain('<select')
            ->and($c->render())->not->toContain('x-data');
    });
});
