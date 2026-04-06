# Modal

A fully accessible modal dialog driven by Alpine.js custom events — no Livewire entangle needed.

### Opening and closing

Open any modal from anywhere on the page by dispatching the `open-modal` browser event with the modal's `id` as the detail:

```blade
{{-- From plain HTML / Alpine --}}
<button @click="$dispatch('open-modal', 'confirm-delete')" class="btn btn-error">
    Delete
</button>

{{-- From Livewire --}}
<button wire:click="$dispatch('open-modal', 'confirm-delete')" class="btn btn-error">
    Delete
</button>

{{-- From JS --}}
<script>window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-delete' }))</script>
```

Close with `$dispatch('close-modal', 'id')` or press **Escape** (when `closeable` is true).

---

### Basic modal

```blade
<x-tallui-modal id="welcome" title="Welcome to TallUI">
    <p>This is the modal body. Put any content here.</p>
</x-tallui-modal>

<button @click="$dispatch('open-modal', 'welcome')" class="btn btn-primary">Open</button>
```

---

### Confirm / danger dialog

```blade
<x-tallui-modal
    id="confirm-delete"
    title="Delete User"
    icon="o-trash"
    icon-color="text-error"
    size="sm"
>
    <p class="text-base-content/70">
        Are you sure you want to delete <strong>{{ $user->name }}</strong>?
        This action cannot be undone.
    </p>

    <x-slot:footer>
        <button class="btn btn-ghost" @click="$dispatch('close-modal', 'confirm-delete')">
            Cancel
        </button>
        <button class="btn btn-error" wire:click="deleteUser({{ $user->id }})">
            Yes, delete
        </button>
    </x-slot:footer>
</x-tallui-modal>
```

---

### With inline trigger slot

Wrap the trigger inside the component so it's co-located with the modal definition:

```blade
<x-tallui-modal id="edit-profile" title="Edit Profile" size="lg">
    <x-slot:trigger>
        <button class="btn btn-sm btn-outline">Edit Profile</button>
    </x-slot:trigger>

    <livewire:profile-form />

    <x-slot:footer>
        <button class="btn btn-ghost" @click="$dispatch('close-modal', 'edit-profile')">Cancel</button>
        <button class="btn btn-primary" wire:click="save">Save changes</button>
    </x-slot:footer>
</x-tallui-modal>
```

---

### Non-closeable modal (e.g. loading/processing)

```blade
<x-tallui-modal id="processing" title="Processing payment..." :closeable="false" size="sm">
    <div class="flex flex-col items-center gap-4 py-4">
        <x-tallui-loading variant="ring" size="lg" color="text-primary" />
        <p class="text-sm text-base-content/60">Please do not close this window.</p>
    </div>
</x-tallui-modal>
```

---

### Open from Livewire action

```php
// In your Livewire component
public function confirmDelete(int $id): void
{
    $this->pendingDeleteId = $id;
    $this->dispatch('open-modal', 'confirm-delete');
}

public function deleteUser(): void
{
    User::findOrFail($this->pendingDeleteId)->delete();
    $this->dispatch('close-modal', 'confirm-delete');
    $this->success('User deleted.');
}
```

---

### Props

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `id` | `string` | `'modal'` | Unique identifier — used in `open-modal` / `close-modal` events |
| `title` | `string` | `''` | Header title text |
| `size` | `string` | `'md'` | `sm` (max-w-sm) \| `md` (max-w-lg) \| `lg` (max-w-2xl) \| `xl` (max-w-4xl) \| `full` |
| `closeable` | `bool` | `true` | Show ✕ button, allow backdrop click and Escape to close |
| `icon` | `?string` | `null` | Heroicon name shown beside the title |
| `iconColor` | `string` | `'text-primary'` | Icon colour class |

### Slots

| Slot | Description |
| --- | --- |
| `default` | Modal body content |
| `trigger` | Optional trigger element (rendered outside the overlay, clicking it opens the modal) |
| `footer` | Action buttons row, right-aligned below the body |

---

← [Back to docs](../README.md)
