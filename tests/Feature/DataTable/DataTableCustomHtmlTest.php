<?php

declare(strict_types = 1);

use Centrex\TallUi\Tests\Fixtures\DataTables\{HtmlColumnsTable, UsersTable};
use Centrex\TallUi\Tests\Fixtures\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    User::create(['name' => 'Alice Smith', 'email' => 'alice@test.com', 'status' => 'active']);
    User::create(['name' => 'Bob Johnson', 'email' => 'bob@test.com',   'status' => 'inactive']);
});

describe('html() renderer column', function (): void {
    it('renders output from ColumnRenderer class', function (): void {
        livewire(HtmlColumnsTable::class)
            ->assertSee('color:green') // StatusRenderer output for 'active'
            ->assertSee('color:orange'); // StatusRenderer output for 'inactive'
    });

    it('escapes the value inside the renderer output', function (): void {
        User::create(['name' => '<script>xss</script>', 'email' => 'xss@test.com', 'status' => 'active']);

        livewire(HtmlColumnsTable::class)
            ->assertDontSee('<script>xss</script>'); // escaped by renderer's e()
    });
});

describe('raw() column', function (): void {
    it('renders value without escaping', function (): void {
        // raw() should echo the field value as-is
        livewire(HtmlColumnsTable::class)
            ->assertSee('Alice Smith'); // raw name column renders the value
    });
});

describe('badge() with color map', function (): void {
    it('applies success color for active status', function (): void {
        livewire(UsersTable::class)
            ->assertSee('badge-success');
    });

    it('applies warning color for inactive status', function (): void {
        livewire(UsersTable::class)
            ->assertSee('badge-warning');
    });

    it('falls back to default badge color for unknown values', function (): void {
        User::create(['name' => 'Unknown User', 'email' => 'unknown@test.com', 'status' => 'pending']);

        livewire(UsersTable::class)
            ->assertSee('badge-neutral'); // default fallback
    });
});

describe('renderHtmlColumn()', function (): void {
    it('falls back to escaped plain text for unknown html types', function (): void {
        $component = new HtmlColumnsTable();

        $col = [
            'key'          => 'name',
            'htmlView'     => null,
            'htmlRenderer' => null,
        ];

        $result = $component->renderHtmlColumn($col, ['name' => '<b>bold</b>']);

        expect($result)->toBe('&lt;b&gt;bold&lt;/b&gt;');
    });
});
