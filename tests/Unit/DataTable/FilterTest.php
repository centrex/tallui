<?php

declare(strict_types = 1);

use Centrex\TallUi\DataTable\Filter;

describe('Filter factories', function (): void {
    it('creates a text filter', function (): void {
        $f = Filter::text('Name', 'name');

        expect($f->label)->toBe('Name')
            ->and($f->column)->toBe('name')
            ->and($f->type)->toBe(Filter::TYPE_TEXT);
    });

    it('creates a select filter with options', function (): void {
        $f = Filter::select('Status', 'status', ['active' => 'Active', 'inactive' => 'Inactive']);

        expect($f->type)->toBe(Filter::TYPE_SELECT)
            ->and($f->options)->toBe(['active' => 'Active', 'inactive' => 'Inactive']);
    });

    it('creates a date filter', function (): void {
        $f = Filter::date('Created', 'created_at');

        expect($f->type)->toBe(Filter::TYPE_DATE);
    });

    it('creates a date range filter', function (): void {
        $f = Filter::dateRange('Period', 'started_at', 'ended_at');

        expect($f->type)->toBe(Filter::TYPE_DATE_RANGE)
            ->and($f->column)->toBe('started_at')
            ->and($f->toColumn)->toBe('ended_at');
    });

    it('defaults toColumn to column for dateRange', function (): void {
        $f = Filter::dateRange('Created', 'created_at');

        expect($f->toColumn)->toBe('created_at');
    });

    it('creates a boolean filter', function (): void {
        $f = Filter::boolean('Active', 'is_active');

        expect($f->type)->toBe(Filter::TYPE_BOOLEAN);
    });
});

describe('Filter fluent modifiers', function (): void {
    it('sets placeholder', function (): void {
        $f = Filter::text('Name', 'name')->placeholder('Search…');

        expect($f->placeholder)->toBe('Search…');
    });

    it('enables multiple on select', function (): void {
        $f = Filter::select('Tags', 'tag', [])->multiple();

        expect($f->multiple)->toBeTrue();
    });
});

describe('Filter::stateKeys()', function (): void {
    it('returns single key for text filter', function (): void {
        expect(Filter::text('Name', 'name')->stateKeys())->toBe(['name']);
    });

    it('returns single key for select filter', function (): void {
        expect(Filter::select('Status', 'status', [])->stateKeys())->toBe(['status']);
    });

    it('returns two keys for date range filter', function (): void {
        $keys = Filter::dateRange('Period', 'started_at', 'ended_at')->stateKeys();

        expect($keys)->toBe(['started_at_from', 'ended_at_to']);
    });

    it('returns two identical-column keys when toColumn not given', function (): void {
        $keys = Filter::dateRange('Created', 'created_at')->stateKeys();

        expect($keys)->toBe(['created_at_from', 'created_at_to']);
    });
});

describe('Filter::toArray()', function (): void {
    it('serializes all fields', function (): void {
        $arr = Filter::select('Status', 'status', ['active' => 'Active'])
            ->placeholder('Any')
            ->toArray();

        expect($arr)
            ->toHaveKey('label', 'Status')
            ->toHaveKey('column', 'status')
            ->toHaveKey('type', Filter::TYPE_SELECT)
            ->toHaveKey('options', ['active' => 'Active'])
            ->toHaveKey('placeholder', 'Any')
            ->toHaveKey('stateKeys', ['status']);
    });
});
