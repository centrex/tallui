<?php

declare(strict_types = 1);

use Centrex\TallUi\View\Components\Form\{Checkbox, DatePicker, FormGroup, Radio, Textarea, Toggle};

describe('FormGroup', function (): void {
    it('has no label by default', function (): void {
        expect((new FormGroup())->label)->toBeNull();
    });

    it('accepts label', function (): void {
        expect((new FormGroup(label: 'Email'))->label)->toBe('Email');
    });

    it('template shows required asterisk when required', function (): void {
        $c = new FormGroup(label: 'Email', required: true);

        expect($c->render())->toContain('text-error');
    });

    it('template shows error over helper when both given', function (): void {
        $c = new FormGroup(helper: 'Hint text', error: 'Error message');

        expect($c->render())->toContain('text-error')
            ->and($c->render())->not->toContain('Hint text');
    });
});

describe('Textarea', function (): void {
    it('defaults rows to 4', function (): void {
        expect((new Textarea(name: 'bio'))->rows)->toBe(4);
    });

    it('accepts custom rows', function (): void {
        expect((new Textarea(name: 'bio', rows: 8))->rows)->toBe(8);
    });

    it('resolves size class', function (): void {
        expect((new Textarea(name: 'bio', size: 'lg'))->sizeClass)->toBe('textarea-lg');
    });

    it('template contains textarea-error when error set', function (): void {
        $c = new Textarea(name: 'bio', error: 'Too long');

        expect($c->render())->toContain('textarea-error');
    });
});

describe('Checkbox', function (): void {
    it('is not checked by default', function (): void {
        expect((new Checkbox(name: 'agree'))->checked)->toBeFalse();
    });

    it('accepts checked prop', function (): void {
        expect((new Checkbox(name: 'agree', checked: true))->checked)->toBeTrue();
    });

    it('accepts color prop', function (): void {
        $c = new Checkbox(name: 'agree', color: 'primary');

        expect($c->color)->toBe('primary');
        expect($c->render())->toContain('checkbox-primary');
    });

    it('template contains checkbox-error when error set', function (): void {
        $c = new Checkbox(name: 'agree', error: 'You must agree');

        expect($c->render())->toContain('checkbox-error');
    });
});

describe('Radio', function (): void {
    it('accepts value prop', function (): void {
        expect((new Radio(name: 'plan', value: 'pro'))->value)->toBe('pro');
    });

    it('generates unique id from name + value', function (): void {
        $c = new Radio(name: 'plan', value: 'pro');

        expect($c->render())->toContain('plan-pro');
    });

    it('accepts color prop', function (): void {
        $c = new Radio(name: 'plan', value: 'pro', color: 'success');

        expect($c->render())->toContain('radio-success');
    });
});

describe('Toggle', function (): void {
    it('defaults color to primary', function (): void {
        expect((new Toggle(name: 'active'))->color)->toBe('primary');
    });

    it('template contains toggle-success with success color', function (): void {
        $c = new Toggle(name: 'active', color: 'success');

        expect($c->render())->toContain('toggle-success');
    });

    it('template contains opacity class when disabled', function (): void {
        $c = new Toggle(name: 'active', disabled: true);

        expect($c->render())->toContain('opacity-60');
    });
});

describe('DatePicker', function (): void {
    it('renders type="date" by default', function (): void {
        $c = new DatePicker(name: 'dob');

        expect($c->render())->toContain('type="{{ $withTime ? \'datetime-local\' : \'date\' }}"');
    });

    it('withTime prop changes input type', function (): void {
        expect((new DatePicker(name: 'at', withTime: true))->withTime)->toBeTrue();
    });

    it('accepts min and max props', function (): void {
        $c = new DatePicker(name: 'dob', min: '2000-01-01', max: '2024-12-31');

        expect($c->min)->toBe('2000-01-01')
            ->and($c->max)->toBe('2024-12-31');
    });

    it('resolves size class', function (): void {
        expect((new DatePicker(name: 'dob', size: 'sm'))->sizeClass)->toBe('input-sm');
    });
});
