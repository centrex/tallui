<?php

declare(strict_types = 1);

use Centrex\TallUi\Tests\Fixtures\DataTables\UsersTable;
use Centrex\TallUi\Tests\Fixtures\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    foreach (range(1, 25) as $i) {
        User::create(['name' => "User $i", 'email' => "user{$i}@test.com"]);
    }
});

it('paginates results using per_page config default', function (): void {
    config(['tallui.datatable.per_page' => 10]);

    livewire(UsersTable::class)
        ->assertSet('perPage', 10);
});

it('changing perPage resets to page 1', function (): void {
    livewire(UsersTable::class)
        ->set('page', 2)
        ->set('perPage', 25)
        ->assertSet('page', 1);
});

it('shows correct result count in footer', function (): void {
    livewire(UsersTable::class)
        ->assertSee('25'); // total rows
});

it('renders per-page options from config', function (): void {
    config(['tallui.datatable.per_page_options' => [5, 10, 20]]);

    livewire(UsersTable::class)
        ->assertSee('5')
        ->assertSee('10')
        ->assertSee('20');
});
