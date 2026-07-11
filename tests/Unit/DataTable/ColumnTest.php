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
        $action = Centrex\TallUi\DataTable\Action::make('Edit');
        $col = Column::make('Actions')->actions([$action]);

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

describe('Column formatting', function (): void {
    it('formats currency with a $ symbol and two decimals by default', function (): void {
        expect(Column::formatWithHint(1234.5, 'currency'))->toBe('$1,234.50');
    });

    it('formats currency with an accounting-style parenthesis for negative values', function (): void {
        expect(Column::formatWithHint(-320.75, 'currency'))->toBe('($320.75)');
    });

    it('supports a currency code suffix for the symbol', function (): void {
        expect(Column::formatWithHint(50, 'currency:EUR'))->toBe('€50.00')
            ->and(Column::formatWithHint(50, 'currency:BDT'))->toBe('৳50.00')
            ->and(Column::formatWithHint(50, 'currency:XYZ'))->toBe('XYZ 50.00');
    });

    it('formats plain numbers with thousands separators', function (): void {
        expect(Column::formatWithHint(1234567, 'number'))->toBe('1,234,567');
    });

    it('formats decimals to the given precision', function (): void {
        expect(Column::formatWithHint(1.5, 'decimal:3'))->toBe('1.500')
            ->and(Column::formatWithHint(1.567, 'decimal'))->toBe('1.57');
    });

    it('formats percentages by multiplying by 100 unless :raw is given', function (): void {
        expect(Column::formatWithHint(0.256, 'percent'))->toBe('25.6%')
            ->and(Column::formatWithHint(25.6, 'percent:raw'))->toBe('25.6%');
    });

    it('formats dates', function (): void {
        expect(Column::formatWithHint('2025-04-25', 'date'))->toBe('Apr 25, 2025');
    });

    it('passes through unformatted or unrecognised values unchanged', function (): void {
        expect(Column::formatWithHint('Acme', null))->toBe('Acme')
            ->and(Column::formatWithHint(null, 'currency'))->toBe('')
            ->and(Column::formatWithHint('x', 'not-a-real-format'))->toBe('x');
    });

    it('->currency() is shorthand for ->format(currency:CODE)', function (): void {
        $col = Column::make('Amount', 'amount')->currency('EUR');

        expect($col->format)->toBe('currency:EUR')
            ->and($col->formatValue(10))->toBe('€10.00');
    });
});

describe('Column alignment', function (): void {
    it('defaults to left alignment with no format', function (): void {
        expect(Column::make('Name', 'name')->resolvedAlign())->toBe('left');
    });

    it('auto-right-aligns numeric formats', function (): void {
        expect(Column::make('Amount', 'amount')->currency()->resolvedAlign())->toBe('right')
            ->and(Column::make('Count', 'count')->format('number')->resolvedAlign())->toBe('right')
            ->and(Column::make('Rate', 'rate')->format('percent')->resolvedAlign())->toBe('right');
    });

    it('does not auto-right-align non-numeric formats like date', function (): void {
        expect(Column::make('Created', 'created_at')->format('date')->resolvedAlign())->toBe('left');
    });

    it('an explicit ->align() call wins over the numeric-format default', function (): void {
        expect(Column::make('Amount', 'amount')->currency()->align('center')->resolvedAlign())->toBe('center');
    });

    it('isNumericFormat() reflects the format base', function (): void {
        expect(Column::make('Amount', 'amount')->currency()->isNumericFormat())->toBeTrue()
            ->and(Column::make('Name', 'name')->isNumericFormat())->toBeFalse();
    });
});

describe('Column::summable()', function (): void {
    it('is false by default', function (): void {
        expect(Column::make('Amount', 'amount')->summable)->toBeFalse();
    });

    it('sets the summable flag', function (): void {
        expect(Column::make('Amount', 'amount')->summable()->summable)->toBeTrue();
    });

    it('is included in toArray()', function (): void {
        $arr = Column::make('Amount', 'amount')->currency()->summable()->toArray();

        expect($arr)->toHaveKey('summable', true)
            ->and($arr)->toHaveKey('align', 'right')
            ->and($arr)->toHaveKey('isNumeric', true);
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
