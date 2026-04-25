# Navigation Components

## MenuItem

Single item inside a Sidebar or Menu. Supports links, badges, separators, section titles, and button variants.

```blade
{{-- Link item (default) --}}
<x-tallui-menu-item
    label="Dashboard"
    icon="o-home"
    :link="route('dashboard')"
    :active="request()->routeIs('dashboard')"
/>

{{-- With badge --}}
<x-tallui-menu-item
    label="Invoices"
    icon="o-document-text"
    :link="route('invoices')"
    badge="12"
    badge-type="warning"
/>

{{-- Section divider --}}
<x-tallui-menu-item :separator="true" />

{{-- Section title --}}
<x-tallui-menu-item section-title="Reports" />

{{-- Button (wire:click action) --}}
<x-tallui-menu-item
    label="Log out"
    icon="o-arrow-right-on-rectangle"
    :as-button="true"
    wire:click="logout"
/>

{{-- Disabled --}}
<x-tallui-menu-item label="Coming Soon" icon="o-star" :disabled="true" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `label` | `string\|null` | `null` | Item text |
| `icon` | `string\|null` | `null` | Heroicon short name, e.g. `o-home` |
| `link` | `string\|null` | `null` | href for the item |
| `active` | `bool` | `false` | Applies active highlight |
| `wire-navigate` | `bool` | `true` | Use `wire:navigate` on the link |
| `badge` | `string\|null` | `null` | Badge text |
| `badge-type` | `string` | `primary` | DaisyUI badge color |
| `separator` | `bool` | `false` | Render a `<hr>` divider instead of an item |
| `section-title` | `string\|null` | `null` | Render a non-clickable section heading |
| `as-button` | `bool` | `false` | Render as `<button>` instead of `<a>` |
| `button-type` | `string` | `button` | HTML `type` on the button element |
| `disabled` | `bool` | `false` | Greys out and prevents interaction |

---

## ListItem

Flexible row component for lists of entities — supports avatars, icons, titles, values, badges, and inline actions.

```blade
{{-- Basic --}}
<x-tallui-list-item title="Rahman Brothers Ltd" subtitle="CUST-001" />

{{-- With avatar --}}
<x-tallui-list-item
    title="Jane Smith"
    subtitle="jane@example.com"
    avatar="/avatars/jane.jpg"
    avatar-alt="Jane Smith"
    value="৳ 24,600"
    sub-value="Outstanding"
/>

{{-- With icon --}}
<x-tallui-list-item
    title="Invoice #INV-2026-042"
    subtitle="Due May 10, 2026"
    icon="o-document-text"
    icon-color="text-primary"
    value="৳ 55,000"
>
    <x-slot:actions>
        <x-tallui-button label="View"   icon="o-eye"    class="btn-ghost btn-xs" :link="route('invoices.show', $invoice)" />
        <x-tallui-button label="Pay"    icon="o-credit-card" class="btn-primary btn-xs" wire:click="pay({{ $invoice->id }})" />
    </x-slot:actions>
</x-tallui-list-item>

{{-- As a link row --}}
<x-tallui-list-item
    title="All Customers"
    :link="route('customers')"
    icon="o-users"
/>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `title` | `string\|null` | `null` | Primary text |
| `subtitle` | `string\|null` | `null` | Secondary text below title |
| `value` | `string\|null` | `null` | Right-aligned primary value |
| `sub-value` | `string\|null` | `null` | Right-aligned secondary value |
| `avatar` | `string\|null` | `null` | Image URL for a circular avatar |
| `avatar-alt` | `string\|null` | `null` | Alt text for avatar |
| `icon` | `string\|null` | `null` | Heroicon name (shown instead of avatar) |
| `icon-color` | `string` | `text-base-content/40` | Tailwind text color class for icon |
| `link` | `string\|null` | `null` | Makes the row clickable as a link |
| `no-wire-navigate` | `bool` | `false` | Disable `wire:navigate` on the link |

| Slot | Description |
| --- | --- |
| `actions` | Buttons rendered on the right side |

---

## Pagination

Wraps Laravel's `LengthAwarePaginator` in a DaisyUI pagination bar.

```blade
{{-- In Livewire component --}}
<x-tallui-pagination
    :paginator="$invoices"
    size=""
    :show-info="true"
    :show-per-page="true"
    :per-page-options="[10, 25, 50]"
    align="center"
/>
```

`$invoices` must be an instance of `Illuminate\Contracts\Pagination\LengthAwarePaginator` — returned by Eloquent's `->paginate($n)`.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `paginator` | `LengthAwarePaginator` | required | The paginator instance |
| `size` | `string` | `''` | `xs` \| `sm` \| `''` (md) \| `lg` |
| `show-info` | `bool` | `true` | "Showing X–Y of Z results" text |
| `show-per-page` | `bool` | `false` | Per-page dropdown selector |
| `per-page-options` | `array` | `[10, 25, 50, 100]` | Options for the per-page selector |
| `align` | `string` | `center` | `start` \| `center` \| `end` |

Typical Livewire usage with `WithPagination`:

```php
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.invoice-list', [
            'invoices' => Invoice::paginate(25),
        ]);
    }
}
```
