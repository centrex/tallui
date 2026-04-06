# DataTable

The `DataTable` is a Livewire component. Extend it in your own Livewire class and override `query()` and `columns()`.

### Basic DataTable Usage

```bash
php artisan make:livewire UsersTable
```

```php
// app/Livewire/UsersTable.php
namespace App\Livewire;

use App\Models\User;
use Centrex\TallUi\DataTable\Action;
use Centrex\TallUi\DataTable\Column;
use Centrex\TallUi\Livewire\DataTable;
use Illuminate\Database\Eloquent\Builder;

class UsersTable extends DataTable
{
    public function query(): Builder
    {
        return User::query()->with('role');
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),
            Column::make('Role', 'role.name'),
            Column::make('Status', 'status')->badge(),
            Column::make('Joined', 'created_at')->sortable(),
            Column::make('Actions')->actions([
                Action::make('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('info')
                    ->route('users.edit', 'id'),

                Action::make('Delete')
                    ->icon('o-trash')
                    ->color('error')
                    ->confirm('Are you sure you want to delete this user?')
                    ->emit('deleteUser', 'id'),
            ]),
        ];
    }
}
```

Use it in a Blade template:

```blade
<livewire:users-table />
```

---

### Column Builder

```php
use Centrex\TallUi\DataTable\Column;

Column::make('Label', 'db_column')   // key supports dot-notation for relations
    ->sortable()                      // adds sort arrows in the header
    ->searchable()                    // included in the global search WHERE clause
    ->badge()                         // renders value inside a <span class="badge">
    ->format('currency');             // named formatter (extensible)
```

**Dot-notation for relations:**

```php
// User belongsTo Role
Column::make('Role', 'role.name')->sortable(),
// Renders: data_get($user, 'role.name')
// Eager-load in query(): User::query()->with('role')
```

---

### Row Actions

```php
use Centrex\TallUi\DataTable\Action;

Action::make('View')
    ->icon('o-eye')
    ->color('ghost')
    ->route('users.show', 'id');        // href="{{ route('users.show', $row->id) }}"

Action::make('Approve')
    ->icon('o-check')
    ->color('success')
    ->emit('approveUser', 'id');        // dispatches Livewire event { id: $row->id }

Action::make('Delete')
    ->icon('o-trash')
    ->color('error')
    ->confirm('Delete this record?')    // uses wire:confirm
    ->emit('deleteUser', 'id');
```

**Listening to emitted events in a parent component:**

```php
#[On('deleteUser')]
public function deleteUser(int $id): void
{
    User::findOrFail($id)->delete();
    $this->dispatch('$refresh');
}
```

---

### Sorting & Searching

Both are handled automatically:

- **Search** — filters all columns marked `.searchable()`, debounced 300ms, URL-synced as `?search=`
- **Sort** — click a sortable column header to toggle asc/desc, URL-synced as `?sort=&dir=`
- **Pagination** — uses Livewire's built-in `WithPagination`, per-page selector in the toolbar
- **URL state** — all three are reflected in the URL via `#[Url]` for bookmarkable/shareable links

---

← [Back to docs](../README.md)
