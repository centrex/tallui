<?php

declare(strict_types = 1);

use Centrex\TallUi\DataTable\Column;

describe('Column::make()', function (): void {
    it('creates a column with label only', function (): void {
        $col = Column::make('Name');

        expect($col->label)->toBe('Name')
            ->and($col->key)->toBeNull();
    });

    it('creates a column with label and key', function (): void {
        $col = Column::make('Email', 'email');

        expect($col->label)->toBe('Email')
            ->and($col->key)->toBe('email');
    });
});

describe('Column flags', function (): void {
    it('is not sortable by default', function (): void {
        expect(Column::make('Name', 'name')->sortable)->toBeFalse();
    });

    it('becomes sortable via sortable()', function (): void {
        expect(Column::make('Name', 'name')->sortable()->sortable)->toBeTrue();
    });

    it('is not searchable by default', function (): void {
        expect(Column::make('Name', 'name')->searchable)->toBeFalse();
    });

    it('becomes searchable via searchable()', function (): void {
        expect(Column::make('Name', 'name')->searchable()->searchable)->toBeTrue();
    });

    it('supports method chaining', function (): void {
        $col = Column::make('Name', 'name')->sortable()->searchable();

        expect($col->sortable)->toBeTrue()
            ->and($col->searchable)->toBeTrue();
    });
});

describe('Column::badge()', function (): void {
    it('sets isBadge flag', function (): void {
        $col = Column::make('Status', 'status')->badge();

        expect($col->isBadge)->toBeTrue();
    });

    it('uses neutral as default badge color', function (): void {
        $col = Column::make('Status', 'status')->badge();

        expect($col->badgeColor)->toBe('neutral');
    });

    it('accepts a custom default badge color', function (): void {
        $col = Column::make('Status', 'status')->badge('primary');

        expect($col->badgeColor)->toBe('primary');
    });

    it('accepts per-value color map', function (): void {
        $col = Column::make('Status', 'status')->badge('neutral', [
            'active'   => 'success',
            'inactive' => 'error',
        ]);

        expect($col->badgeColors)->toBe(['active' => 'success', 'inactive' => 'error']);
    });

    it('resolves badge color from value map', function (): void {
        $col = Column::make('Status', 'status')->badge('neutral', ['active' => 'success']);

        expect($col->resolveBadgeColor('active'))->toBe('success')
            ->and($col->resolveBadgeColor('unknown'))->toBe('neutral');
    });
});

describe('Column HTML rendering', function (): void {
    it('marks column as raw HTML via raw()', function (): void {
        $col = Column::make('Excerpt', 'excerpt')->raw();

        expect($col->isRaw)->toBeTrue()
            ->and($col->isHtml)->toBeFalse();
    });

    it('sets htmlView via view()', function (): void {
        $col = Column::make('Avatar', 'name')->view('tables.avatar');

        expect($col->isHtml)->toBeTrue()
            ->and($col->htmlView)->toBe('tables.avatar')
            ->and($col->htmlRenderer)->toBeNull();
    });

    it('sets htmlRenderer via html()', function (): void {
        $col = Column::make('Status', 'status')->html('App\\Renderers\\StatusRenderer');

        expect($col->isHtml)->toBeTrue()
            ->and($col->htmlRenderer)->toBe('App\\Renderers\\StatusRenderer')
            ->and($col->htmlView)->toBeNull();
    });
});

describe('Column::actions()', function (): void {
    it('sets isActions flag', function (): void {
        $col = Column::make('Actions')->actions([]);

        expect($col->isActions)->toBeTrue();
    });

    it('stores actions array', function (): void {
        $action = \Centrex\TallUi\DataTable\Action::make('Edit');
        $col    = Column::make('Actions')->actions([$action]);

        expect($col->actions)->toHaveCount(1)
            ->and($col->actions[0])->toBe($action);
    });
});

describe('Column::getValue()', function (): void {
    it('returns null when key is null', function (): void {
        $col = Column::make('Actions');
        expect($col->getValue(['name' => 'Alice']))->toBeNull();
    });

    it('resolves simple key from array', function (): void {
        $col = Column::make('Name', 'name');
        expect($col->getValue(['name' => 'Alice']))->toBe('Alice');
    });

    it('resolves dot-notation key', function (): void {
        $col = Column::make('City', 'address.city');
        expect($col->getValue(['address' => ['city' => 'London']]))->toBe('London');
    });
});

describe('Column::toArray()', function (): void {
    it('serializes all fields', function (): void {
        $arr = Column::make('Name', 'name')
            ->sortable()
            ->searchable()
            ->badge('success')
            ->toArray();

        expect($arr)
            ->toHaveKey('label', 'Name')
            ->toHaveKey('key', 'name')
            ->toHaveKey('sortable', true)
            ->toHaveKey('searchable', true)
            ->toHaveKey('isBadge', true)
            ->toHaveKey('badgeColor', 'success')
            ->toHaveKey('isActions', false)
            ->toHaveKey('isRaw', false)
            ->toHaveKey('isHtml', false)
            ->toHaveKey('actions', []);
    });
});
