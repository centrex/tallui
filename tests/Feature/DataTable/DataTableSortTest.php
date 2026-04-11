<?php

declare(strict_types = 1);

use Centrex\TallUi\Tests\Fixtures\DataTables\UsersTable;
use Centrex\TallUi\Tests\Fixtures\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    User::create(['name' => 'Charlie Brown', 'email' => 'charlie@test.com']);
    User::create(['name' => 'Alice Smith',   'email' => 'alice@test.com']);
    User::create(['name' => 'Bob Johnson',   'email' => 'bob@test.com']);
});

it('sorts ascending on first click', function (): void {
    $component = livewire(UsersTable::class)
        ->call('sort', 'name')
        ->assertSet('sortBy', 'name')
        ->assertSet('sortDirection', 'asc');

    // First row should be Alice (alphabetically first)
    $component->assertSeeInOrder(['Alice Smith', 'Bob Johnson', 'Charlie Brown']);
});

it('toggles to descending on second click of the same column', function (): void {
    livewire(UsersTable::class)
        ->call('sort', 'name')
        ->call('sort', 'name')
        ->assertSet('sortBy', 'name')
        ->assertSet('sortDirection', 'desc');
});

it('resets direction to asc when clicking a different column', function (): void {
    livewire(UsersTable::class)
        ->call('sort', 'name')
        ->call('sort', 'name') // now desc
        ->call('sort', 'email') // different column → back to asc
        ->assertSet('sortBy', 'email')
        ->assertSet('sortDirection', 'asc');
});

it('sort resets to page 1', function (): void {
    foreach (range(1, 20) as $i) {
        User::create(['name' => "User {$i}", 'email' => "user{$i}@test.com"]);
    }

    livewire(UsersTable::class)
        ->set('page', 2)
        ->call('sort', 'name')
        ->assertSet('page', 1);
});

it('ignores invalid sort columns', function (): void {
    livewire(UsersTable::class)
        ->call('sort', 'created_at')
        ->assertSet('sortBy', 'name')
        ->assertSet('sortDirection', 'asc');
});
