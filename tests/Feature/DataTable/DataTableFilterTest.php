<?php

declare(strict_types = 1);

use Centrex\TallUi\Tests\Fixtures\DataTables\FilteredUsersTable;
use Centrex\TallUi\Tests\Fixtures\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    User::create(['name' => 'Alice Smith',  'email' => 'alice@test.com',  'status' => 'active',   'is_active' => true,  'joined_at' => '2024-01-15']);
    User::create(['name' => 'Bob Johnson',  'email' => 'bob@test.com',    'status' => 'inactive', 'is_active' => false, 'joined_at' => '2024-03-10']);
    User::create(['name' => 'Carol White',  'email' => 'carol@test.com',  'status' => 'active',   'is_active' => true,  'joined_at' => '2024-06-20']);
    User::create(['name' => 'Dave Black',   'email' => 'dave@test.com',   'status' => 'inactive', 'is_active' => false, 'joined_at' => '2023-11-05']);
});

describe('filter panel', function (): void {
    it('toggles the filter panel open and closed', function (): void {
        livewire(FilteredUsersTable::class)
            ->assertSet('filtersOpen', false)
            ->call('toggleFilters')
            ->assertSet('filtersOpen', true)
            ->call('toggleFilters')
            ->assertSet('filtersOpen', false);
    });

    it('exposes filter definitions to the view', function (): void {
        livewire(FilteredUsersTable::class)
            ->call('toggleFilters')
            ->assertSee('Status') // filter label visible in panel
            ->assertSee('Active');
    });
});

describe('select filter', function (): void {
    it('filters by status = active', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('tableFilters.status', 'active')
            ->assertSee('Alice Smith')
            ->assertSee('Carol White')
            ->assertDontSee('Bob Johnson')
            ->assertDontSee('Dave Black');
    });

    it('filters by status = inactive', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('tableFilters.status', 'inactive')
            ->assertSee('Bob Johnson')
            ->assertSee('Dave Black')
            ->assertDontSee('Alice Smith')
            ->assertDontSee('Carol White');
    });

    it('shows all rows when status filter is empty', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('tableFilters.status', 'active')
            ->set('tableFilters.status', '') // clear
            ->assertSee('Alice Smith')
            ->assertSee('Bob Johnson');
    });
});

describe('boolean filter', function (): void {
    it('filters to active-only users', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('tableFilters.is_active', '1')
            ->assertSee('Alice Smith')
            ->assertDontSee('Bob Johnson');
    });

    it('filters to inactive-only users', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('tableFilters.is_active', '0')
            ->assertSee('Bob Johnson')
            ->assertDontSee('Alice Smith');
    });
});

describe('date range filter', function (): void {
    it('filters rows on or after from date', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('tableFilters.joined_at_from', '2024-03-01')
            ->assertSee('Bob Johnson')   // 2024-03-10
            ->assertSee('Carol White')   // 2024-06-20
            ->assertDontSee('Dave Black'); // 2023-11-05
    });

    it('filters rows on or before to date', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('tableFilters.joined_at_to', '2024-02-01')
            ->assertSee('Alice Smith')  // 2024-01-15
            ->assertSee('Dave Black')   // 2023-11-05
            ->assertDontSee('Carol White'); // 2024-06-20
    });

    it('filters rows within a date range', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('tableFilters.joined_at_from', '2024-01-01')
            ->set('tableFilters.joined_at_to', '2024-04-01')
            ->assertSee('Alice Smith')   // 2024-01-15 ✓
            ->assertSee('Bob Johnson')   // 2024-03-10 ✓
            ->assertDontSee('Carol White')  // 2024-06-20 outside range
            ->assertDontSee('Dave Black');  // 2023-11-05 outside range
    });
});

describe('filter state management', function (): void {
    it('activeFilterCount returns 0 when no filters set', function (): void {
        livewire(FilteredUsersTable::class)
            ->assertSet('tableFilters', []);

        $component = livewire(FilteredUsersTable::class);

        expect($component->instance()->activeFilterCount())->toBe(0);
    });

    it('activeFilterCount counts non-empty filters', function (): void {
        $component = livewire(FilteredUsersTable::class)
            ->set('tableFilters.status', 'active')
            ->set('tableFilters.is_active', '1');

        expect($component->instance()->activeFilterCount())->toBe(2);
    });

    it('resetFilters clears all filters and closes panel', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('filtersOpen', true)
            ->set('tableFilters.status', 'active')
            ->call('resetFilters')
            ->assertSet('filtersOpen', false)
            ->assertSet('tableFilters', []);
    });

    it('resetFilter clears a single filter key', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('tableFilters.status', 'active')
            ->set('tableFilters.is_active', '1')
            ->call('resetFilter', 'status')
            ->assertSet('tableFilters', ['status' => null, 'is_active' => '1']);
    });

    it('updatedTableFilters resets to page 1', function (): void {
        foreach (range(1, 20) as $i) {
            User::create(['name' => "Extra {$i}", 'email' => "extra{$i}@test.com", 'status' => 'active']);
        }

        livewire(FilteredUsersTable::class)
            ->set('page', 2)
            ->set('tableFilters.status', 'active')
            ->assertSet('page', 1);
    });
});

describe('combined search + filter', function (): void {
    it('applies both search and filter simultaneously', function (): void {
        livewire(FilteredUsersTable::class)
            ->set('search', 'carol') // matches Carol White
            ->set('tableFilters.status', 'inactive') // Carol is active
            ->assertDontSee('Carol White') // filtered out by status
            ->assertDontSee('Alice Smith'); // not in search results
    });
});
