<?php

declare(strict_types = 1);

use Centrex\TallUi\Tests\Fixtures\DataTables\ArrayBackedDataTable;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;

use function Pest\Livewire\livewire;

/**
 * Column-visibility persistence across a fresh mount with no `cols` query
 * param (i.e. a returning visit, not a shared link) for an authenticated
 * user. Uses ArrayBackedDataTable — it never touches the DB, so this file
 * lives outside tests/Feature/DataTable on purpose (see DataTableViewRenderTest).
 */
function preferenceTestRows(): array
{
    return [
        ['id' => 1, 'name' => 'Acme Corp', 'amount' => 15234.5, 'balance' => -320.75, 'status' => 'active'],
    ];
}

function preferenceTestUser(int $id): GenericUser
{
    return new GenericUser(['id' => $id, 'remember_token' => null]);
}

afterEach(function (): void {
    Auth::logout();
});

it('remembers a logged-in user\'s hidden columns across a fresh mount', function (): void {
    Auth::login(preferenceTestUser(1));

    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', preferenceTestRows())
        ->call('toggleColumnVisibility', 'balance')
        ->assertSet('hiddenColumns', ['balance']);

    // A brand-new component/mount, no `cols` param this time — should pick
    // the previously saved preference back up.
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', preferenceTestRows())
        ->assertSet('hiddenColumns', ['balance']);
});

it('scopes remembered columns per user', function (): void {
    Auth::login(preferenceTestUser(1));
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', preferenceTestRows())
        ->call('toggleColumnVisibility', 'balance');
    Auth::logout();

    Auth::login(preferenceTestUser(2));
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', preferenceTestRows())
        ->assertSet('hiddenColumns', []);
});

it('does not persist column choices for guests', function (): void {
    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', preferenceTestRows())
        ->call('toggleColumnVisibility', 'balance')
        ->assertSet('hiddenColumns', ['balance']);

    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', preferenceTestRows())
        ->assertSet('hiddenColumns', []);
});

it('does not persist when the host component opts out via persistColumnPreferences', function (): void {
    Auth::login(preferenceTestUser(3));

    livewire(ArrayBackedDataTable::class)
        ->set('persistColumnPreferences', false)
        ->set('fakeRows', preferenceTestRows())
        ->call('toggleColumnVisibility', 'balance');

    livewire(ArrayBackedDataTable::class)
        ->set('fakeRows', preferenceTestRows())
        ->assertSet('hiddenColumns', []);
});
