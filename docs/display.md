# Display Components

## Collapse

Expandable section using DaisyUI's `collapse` component. Backed by Alpine `x-data` so state is client-side.

```blade
{{-- Arrow indicator (default) --}}
<x-tallui-collapse title="What payment methods do you accept?" :open="false" variant="arrow">
    We accept bank transfer, mobile banking (bKash / Nagad), and cheque.
</x-tallui-collapse>

{{-- Plus indicator --}}
<x-tallui-collapse title="Shipping policy" variant="plus" :bordered="true">
    All orders ship within 2 business days.
</x-tallui-collapse>

{{-- No indicator, custom title classes --}}
<x-tallui-collapse
    title="Advanced filters"
    variant="none"
    title-class="font-semibold text-primary"
    content-class="bg-base-200 rounded-b-lg"
>
    {{-- filter form --}}
</x-tallui-collapse>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `title` | `string` | `''` | Clickable header text |
| `open` | `bool` | `false` | Initially expanded |
| `bordered` | `bool` | `false` | Adds `collapse-bordered` class |
| `variant` | `string` | `arrow` | `arrow` \| `plus` \| `none` |
| `title-class` | `string` | `''` | Extra classes on the title element |
| `content-class` | `string` | `''` | Extra classes on the content wrapper |

---

## Dialog

Standalone alert/confirm dialog (native `<dialog>` element). Distinct from [Modal](modal.md) — lighter, no Livewire dependency, suited for simple confirmations and status messages.

```blade
{{-- Info / warning / error --}}
<x-tallui-dialog
    id="low-stock-alert"
    title="Low Stock Warning"
    type="warning"
    size="sm"
>
    Samsung TV 55" has only 3 units remaining.

    <x-slot:actions>
        <x-tallui-button label="View Inventory" class="btn-warning" @click="$refs['low-stock-alert'].close()" />
    </x-slot:actions>
</x-tallui-dialog>

{{-- Confirm / danger --}}
<x-tallui-dialog
    id="delete-confirm"
    title="Delete Invoice?"
    type="error"
    :closeable="true"
    size="sm"
>
    This action cannot be undone. Invoice #INV-2026-042 will be permanently deleted.

    <x-slot:actions>
        <x-tallui-button label="Cancel"  class="btn-ghost" @click="document.getElementById('delete-confirm').close()" />
        <x-tallui-button label="Delete"  class="btn-error" wire:click="deleteInvoice" />
    </x-slot:actions>
</x-tallui-dialog>

{{-- Open the dialog --}}
<x-tallui-button label="Delete" class="btn-error" @click="document.getElementById('delete-confirm').showModal()" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `id` | `string\|null` | auto UUID | HTML id attribute |
| `title` | `string` | `''` | Dialog heading |
| `type` | `string` | `info` | `info` \| `success` \| `warning` \| `error` \| `confirm` |
| `icon` | `string\|null` | auto per type | Override the auto-selected heroicon |
| `closeable` | `bool` | `true` | Show × close button |
| `size` | `string` | `sm` | `sm` \| `md` \| `lg` |

Auto-selected icons: `success` → `o-check-circle`, `warning` → `o-exclamation-triangle`, `error` → `o-x-circle`.

| Slot | Description |
| --- | --- |
| `actions` | Buttons rendered in the dialog footer |

---

## ImageLibrary

Selectable image grid — renders thumbnails and tracks selection state in Alpine. Optionally emits hidden form inputs for server-side use.

```blade
{{-- Display only --}}
<x-tallui-image-library
    :images="[
        ['src' => '/img/samsung-tv.jpg', 'alt' => 'Samsung TV', 'id' => 1],
        ['src' => '/img/samsung-fridge.jpg', 'caption' => 'Fridge', 'id' => 2],
    ]"
    :selectable="false"
    :columns="4"
    height="h-32"
    fit="cover"
/>

{{-- Selectable (single) --}}
<x-tallui-image-library
    :images="$productImages"
    :selectable="true"
    :multiple="false"
    :selected="[$product->primary_image_id]"
    name="primary_image_id"
    height="h-40"
    :columns="3"
/>

{{-- Multi-select --}}
<x-tallui-image-library
    :images="$galleryImages"
    :multiple="true"
    :selected="$selectedIds"
    name="gallery_image_ids"
/>
```

Each item in `:images` is an associative array with keys:

| Key | Required | Description |
| --- | --- | --- |
| `src` | yes | Image URL |
| `id` | recommended | Used for selection tracking and hidden inputs |
| `alt` | no | Alt text |
| `caption` | no | Text below the thumbnail |

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `images` | `array` | `[]` | Array of image arrays |
| `multiple` | `bool` | `false` | Allow selecting multiple images |
| `selected` | `array` | `[]` | Pre-selected image ids or srcs |
| `selectable` | `bool` | `true` | Enable selection UI |
| `fit` | `string` | `cover` | `cover` \| `contain` — CSS `object-fit` |
| `height` | `string` | `h-32` | Tailwind height class for thumbnails |
| `columns` | `int` | `4` | Grid column count |
| `name` | `string\|null` | `null` | If set, renders hidden `<input>` elements for form submission |

---

## Icon

Renders a single Blade Heroicons icon. Accepts a short name (`o-home`, `s-star`) or a full prefixed name (`heroicon-o-home`). The prefix is resolved from `config('blade-heroicons.prefix')`.

```blade
{{-- Short name --}}
<x-tallui-icon name="o-check-circle" size="w-6 h-6" />

{{-- Full name --}}
<x-tallui-icon name="heroicon-s-star" size="w-5 h-5" class="text-warning" />

{{-- With label (for accessibility) --}}
<x-tallui-icon name="o-user" label="Profile" size="w-5 h-5" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string\|null` | `null` | Heroicon name (short or full) |
| `size` | `string` | `w-5 h-5` | Tailwind size classes |
| `label` | `string\|null` | `null` | `aria-label` for screen readers |
| `id` | `string\|null` | `null` | HTML id attribute |

Accepts additional `$attributes` (e.g. `class="text-error"`) which are merged onto the icon element.
