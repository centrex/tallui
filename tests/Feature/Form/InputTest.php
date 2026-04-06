<?php

declare(strict_types = 1);

use Centrex\TallUi\View\Components\Form\Input;

// Helper: render a component's inline BLADE template string
function renderInput(Input $component): string
{
    return $component->render();
}

describe('Input component properties', function (): void {
    it('has correct default type', function (): void {
        $c = new Input(name: 'field');

        expect($c->type)->toBe('text');
    });

    it('accepts name prop', function (): void {
        $c = new Input(name: 'email');

        expect($c->name)->toBe('email');
    });

    it('accepts type prop', function (): void {
        $c = new Input(name: 'pwd', type: 'password');

        expect($c->type)->toBe('password');
    });

    it('accepts label prop', function (): void {
        $c = new Input(name: 'email', label: 'Email Address');

        expect($c->label)->toBe('Email Address');
    });

    it('accepts error prop', function (): void {
        $c = new Input(name: 'email', error: 'Invalid email');

        expect($c->error)->toBe('Invalid email');
    });

    it('required defaults to false', function (): void {
        expect((new Input(name: 'field'))->required)->toBeFalse();
    });

    it('disabled defaults to false', function (): void {
        expect((new Input(name: 'field'))->disabled)->toBeFalse();
    });
});

describe('Input size class resolution', function (): void {
    it('defaults to md size class', function (): void {
        $c = new Input(name: 'field');

        expect($c->sizeClass)->toBe('input-md');
    });

    it('resolves xs size', function (): void {
        expect((new Input(name: 'f', size: 'xs'))->sizeClass)->toBe('input-xs');
    });

    it('resolves sm size', function (): void {
        expect((new Input(name: 'f', size: 'sm'))->sizeClass)->toBe('input-sm');
    });

    it('resolves lg size', function (): void {
        expect((new Input(name: 'f', size: 'lg'))->sizeClass)->toBe('input-lg');
    });

    it('falls back to config size when not specified', function (): void {
        config(['tallui.forms.size' => 'sm']);

        expect((new Input(name: 'f'))->sizeClass)->toBe('input-sm');
    });
});

describe('Input template content', function (): void {
    it('template contains input-error when error is set', function (): void {
        $c = new Input(name: 'email', error: 'Required');

        expect(renderInput($c))->toContain('input-error');
    });

    it('template contains label-text when label is set', function (): void {
        $c = new Input(name: 'email', label: 'Email');

        expect(renderInput($c))->toContain('label-text');
    });

    it('template uses input input-bordered wrapper when icon is set', function (): void {
        $c = new Input(name: 'email', icon: 'o-envelope');

        expect(renderInput($c))->toContain('input input-bordered');
    });

    it('template contains opacity class when disabled', function (): void {
        $c = new Input(name: 'field', disabled: true);

        expect(renderInput($c))->toContain('opacity-60');
    });
});
