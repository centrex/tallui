<?php

declare(strict_types = 1);

use Centrex\TallUi\Tests\Fixtures\DataTables\UsersTable;
use Centrex\TallUi\Tests\Fixtures\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    User::create(['name' => 'Alice Smith',   'email' => 'alice@test.com']);
    User::create(['name' => 'Bob Johnson',   'email' => 'bob@test.com']);
    User::create(['name' => 'Charlie Brown', 'email' => 'charlie@test.com']);
});

it('filters rows by search term matching name', function (): void {
    livewire(UsersTable::class)
        ->set('search', 'Alice')
        ->assertSee('Alice Smith')
        ->assertDontSee('Bob Johnson')
        ->assertDontSee('Charlie Brown');
});

it('filters rows by search term matching email', function (): void {
    livewire(UsersTable::class)
        ->set('search', 'bob@')
        ->assertSee('Bob Johnson')
        ->assertDontSee('Alice Smith');
});

it('search is case insensitive', function (): void {
    livewire(UsersTable::class)
        ->set('search', 'alice')
        ->assertSee('Alice Smith');
});

it('shows no results for unmatched search', function (): void {
    livewire(UsersTable::class)
        ->set('search', 'zzznomatch')
        ->assertSee('No results found');
});

it('clearSearch resets the search term', function (): void {
    livewire(UsersTable::class)
        ->set('search', 'Alice')
        ->call('clearSearch')
        ->assertSet('search', '')
        ->assertSee('Alice Smith')
        ->assertSee('Bob Johnson');
});

it('resets to page 1 on new search', function (): void {
    livewire(UsersTable::class)
        ->set('search', 'Alice')
        ->assertSet('page', 1);
});

it('does not apply search when below minSearchLength', function (): void {
    livewire(UsersTable::class)
        ->set('minSearchLength', 4)
        ->set('search', 'Ali') // only 3 chars — below threshold
        ->assertSee('Bob Johnson') // all rows still visible
        ->assertSee('Charlie Brown');
});

it('applies search once minSearchLength is met', function (): void {
    livewire(UsersTable::class)
        ->set('minSearchLength', 3)
        ->set('search', 'Ali') // exactly 3 chars — threshold met
        ->assertSee('Alice Smith')
        ->assertDontSee('Bob Johnson');
});

it('updatedSearch resets page', function (): void {
    // Create enough rows to have multiple pages
    foreach (range(1, 20) as $i) {
        User::create(['name' => "Extra User {$i}", 'email' => "extra{$i}@test.com"]);
    }

    livewire(UsersTable::class)
        ->set('page', 2)
        ->set('search', 'Alice')
        ->assertSet('page', 1);
});
