<?php

declare(strict_types = 1);

use Centrex\TallUi\Tests\Fixtures\DataTables\UsersTable;
use Centrex\TallUi\Tests\Fixtures\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    User::create(['name' => 'Alice Smith',  'email' => 'alice@test.com',  'status' => 'active']);
    User::create(['name' => 'Bob Johnson',  'email' => 'bob@test.com',    'status' => 'inactive']);
    User::create(['name' => 'Carol White',  'email' => 'carol@test.com',  'status' => 'active']);
});

it('renders the datatable component', function (): void {
    livewire(UsersTable::class)
        ->assertOk()
        ->assertSee('Alice Smith')
        ->assertSee('Bob Johnson')
        ->assertSee('Carol White');
});

it('renders column headers', function (): void {
    livewire(UsersTable::class)
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('Status');
});

it('renders badge for status column', function (): void {
    livewire(UsersTable::class)
        ->assertSee('badge');
});

it('renders pagination when rows exceed per page', function (): void {
    // Create enough rows to trigger pagination
    foreach (range(1, 20) as $i) {
        User::create(['name' => "User $i", 'email' => "user{$i}@test.com"]);
    }

    livewire(UsersTable::class)
        ->assertSee('Showing');
});

it('shows empty state when no results', function (): void {
    User::query()->delete();

    livewire(UsersTable::class)
        ->assertSee('No results found');
});

it('shows total result count', function (): void {
    livewire(UsersTable::class)
        ->assertSee('3'); // total
});
