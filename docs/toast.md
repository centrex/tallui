# Toast Notifications

Toast notifications are driven by the `Toast` trait — use it in any Livewire component to fire non-blocking alerts.

### Setup

Place `<x-tallui-toast />` once in your layout, just before `@livewireScripts`:

```blade
<body>
    {{ $slot }}

    <x-tallui-toast />   {{-- renders the Alpine.js toast container --}}

    @livewireScripts
</body>
```

Optional props:

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `position` | `string` | `'end bottom'` | DaisyUI toast position: `start`/`center`/`end` + `top`/`middle`/`bottom` |
| `timeout` | `int` | `3000` | Default auto-dismiss delay in ms |

### Usage in Livewire components

```php
use Centrex\TallUi\Traits\Toast;

class UserForm extends Component
{
    use Toast;

    public function save(): void
    {
        // ... save logic ...

        $this->success('Saved!', 'User was updated successfully.');
    }

    public function delete(): void
    {
        // ... delete logic ...

        $this->warning('Deleted', 'The user has been removed.', redirectTo: route('users.index'));
    }
}
```

### Available methods

```php
// Convenience wrappers
$this->success('Title', 'Optional description');
$this->error('Title', 'Optional description');
$this->warning('Title', 'Optional description');
$this->info('Title', 'Optional description');

// Full control
$this->toast(
    type:        'success',
    title:       'Payment received',
    description: 'Invoice #1042 has been paid.',
    icon:        'o-currency-dollar',   // any Heroicon name
    css:         'alert-success',       // any DaisyUI alert modifier
    timeout:     5000,                  // ms before auto-dismiss
    redirectTo:  route('dashboard'),    // optional redirect after showing toast
);
```

---

← [Back to docs](../README.md)
