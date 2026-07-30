<?php

declare(strict_types = 1);

use Centrex\TallUi\Tests\Fixtures\DataTables\ArrayBackedDataTable;

use function Pest\Livewire\livewire;

/**
 * Exercises the DataTable Blade view (financial formatting, alignment,
 * sticky header, totals row, striping, select-all-matching) through the
 * ArrayBackedDataTable fixture, which never touches the DB — deliberately
 * kept outside tests/Feature/DataTable so it doesn't inherit that
 * directory's sqlite schema setup (see tests/Pest.php) and can run in
 * environments without a working DB driver.
 */
function dataTableFakeRows(): array
{
    return [
        ['id' => 1, 'name' => 'Acme Corp', 'amount' => 15234.5, 'balance' => -320.75, 'status' => 'active'],
        ['id' => 2, 'name' => 'Globex', 'amount' => 980, 'balance' => 500, 'status' => 'inactive'],
    ];
}

it('renders currency-formatted values with accounting-style negative parentheses', function (): void {
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->assertSee('$15,234.50', false)
        ->assertSee('($320.75)', false);
});

it('applies tabular-nums and a negative-value color class to numeric cells', function (): void {
    $html = livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->html();

    expect($html)->toContain('tabular-nums')
        ->and($html)->toContain('text-error');
});

it('renders a sticky header for financial-grade scrolling tables', function (): void {
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->assertSee('sticky top-0', false);
});

it('renders a formatted totals row for summable columns', function (): void {
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->set('fakeSums', ['amount' => 16214.5, 'balance' => 179.25])
        ->assertSee('$16,214.50', false);
});

it('applies zebra striping when enabled', function (): void {
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->set('striped', true)
        ->assertSee('even:bg-base-200/50', false);
});

it('does not stripe when striped is disabled', function (): void {
    $html = livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->set('striped', false)
        ->html();

    expect($html)->not->toContain('even:bg-base-200/50');
});

it('row hover is a different shade than the zebra stripe so hover stays visible on striped rows', function (): void {
    $html = livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->set('striped', true)
        ->html();

    expect($html)->toContain('hover:bg-base-200')
        ->and($html)->not->toContain('base-50');
});

it('renders the number-range filter inputs when the panel is open', function (): void {
    $html = livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->set('filtersOpen', true)
        ->html();

    expect($html)->toContain('tableFilters.amount_min')
        ->and($html)->toContain('tableFilters.amount_max');
});

it('shows the select-all-matching banner once every row on the page is selected and more rows exist', function (): void {
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->set('fakeTotal', 500)
        ->set('selectedRows', ['1', '2'])
        ->assertSee('Select all 500 matching rows');
});

it('does not show the select-all-matching banner when every matching row already fits on the page', function (): void {
    $html = livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->set('selectedRows', ['1', '2'])
        ->html();

    expect($html)->not->toContain('matching rows');
});

it('selectAllAcrossPages sets the flag and clears explicit row ids', function (): void {
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->set('selectedRows', ['1', '2'])
        ->call('selectAllAcrossPages')
        ->assertSet('selectAllMatching', true)
        ->assertSet('selectedRows', []);
});

it('toggleRow while selectAllMatching starts a fresh single-row selection', function (): void {
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->set('selectAllMatching', true)
        ->call('toggleRow', '1')
        ->assertSet('selectAllMatching', false)
        ->assertSet('selectedRows', ['1']);
});

it('clearSelection resets selectAllMatching too', function (): void {
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', dataTableFakeRows())
        ->set('selectAllMatching', true)
        ->call('clearSelection')
        ->assertSet('selectAllMatching', false)
        ->assertSet('selectedRows', []);
});

describe('header theme', function (): void {
    it('defaults to a solid header darker than the half-opacity zebra stripe', function (): void {
        // Regression: bg-base-50 isn't a real DaisyUI token (only base-100/200/300
        // exist), so the old header/stripe/hover classes silently rendered no
        // background at all. This is the same base-300/base-200/50/base-200
        // header/stripe/hover hierarchy used by every table in the monorepo.
        $html = livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', dataTableFakeRows())
            ->set('striped', true)
            ->html();

        expect($html)->not->toContain('base-50')
            ->and($html)->toContain('bg-base-300')
            ->and($html)->toContain('even:bg-base-200/50')
            ->and($html)->toContain('hover:bg-base-200');
    });

    it('applies the bold header style (heavier text, same base-300 fill)', function (): void {
        $html = livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', dataTableFakeRows())
            ->set('headerStyle', 'bold')
            ->html();

        expect($html)->toContain('bg-base-300')
            ->and($html)->toContain('font-bold');
    });

    it('applies the primary header style', function (): void {
        $html = livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', dataTableFakeRows())
            ->set('headerStyle', 'primary')
            ->html();

        expect($html)->toContain('bg-primary/15');
    });

    it('applies the minimal header style', function (): void {
        $html = livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', dataTableFakeRows())
            ->set('headerStyle', 'minimal')
            ->html();

        expect($html)->toContain('bg-base-100');
    });
});

describe('per-page selector visibility', function (): void {
    it('renders the per-page selector by default', function (): void {
        livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', dataTableFakeRows())
            ->assertSee('wire:model.live="perPage"', false);
    });

    it('hides the per-page selector when disabled', function (): void {
        $html = livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', dataTableFakeRows())
            ->set('showPerPageSelector', false)
            ->html();

        expect($html)->not->toContain('wire:model.live="perPage"');
    });
});

describe('column visibility toggle', function (): void {
    it('renders every toggleable column checked by default', function (): void {
        livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', dataTableFakeRows())
            ->assertSet('hiddenColumns', [])
            ->assertSee('Amount')
            ->assertSee('Balance');
    });

    it('toggleColumnVisibility hides then re-shows a column', function (): void {
        // "Balance" itself still legitimately appears as a label in the
        // column-visibility toggle menu even when hidden from the table, so
        // assert on the formatted cell value instead (unique to this column).
        $component = livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', dataTableFakeRows())
            ->call('toggleColumnVisibility', 'balance')
            ->assertSet('hiddenColumns', ['balance']);

        expect($component->html())->not->toContain('$500.00');

        $component->call('toggleColumnVisibility', 'balance')
            ->assertSet('hiddenColumns', []);

        expect($component->html())->toContain('$500.00');
    });

    it('resetColumnVisibility clears all hidden columns', function (): void {
        livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', dataTableFakeRows())
            ->call('toggleColumnVisibility', 'balance')
            ->call('toggleColumnVisibility', 'amount')
            ->call('resetColumnVisibility')
            ->assertSet('hiddenColumns', []);
    });

    it('a hidden column is excluded from the empty-state colspan count', function (): void {
        $html = livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', [])
            ->call('toggleColumnVisibility', 'balance')
            ->html();

        // 5 columns total minus 1 hidden, plus the checkbox column = 5
        expect($html)->toContain('colspan="5"');
    });

    it('hides the column-visibility toggle when disabled', function (): void {
        $html = livewire(ArrayBackedDataTable::class)
            ->set('fakeRows', dataTableFakeRows())
            ->set('showColumnToggle', false)
            ->html();

        expect($html)->not->toContain('toggleColumnVisibility');
    });
});

describe('lazy-load placeholder', function (): void {
    it('renders a skeleton with one bar per column and a bounded row count', function (): void {
        $table = new ArrayBackedDataTable;
        $html = $table->placeholder()->render();

        expect($html)->toContain('role="status"')
            ->and($html)->toContain('Loading table')
            ->and(substr_count($html, 'skeleton'))->toBeGreaterThan(5);
    });
});
