# Layout Components

## Header

App-level top navigation bar. Renders a brand name/logo on the left and passes default slot content (nav links, user menu) on the right.

```blade
<x-tallui-header
    brand="Acme ERP"
    brand-href="{{ route('dashboard') }}"
    brand-logo="/img/logo.svg"
    :sticky="true"
    :shadow="true"
    height="h-16"
    sidebar-id="main-sidebar"
/>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `brand` | `string\|null` | `null` | Brand name text |
| `brand-href` | `string\|null` | `/` | Link target for brand |
| `brand-logo` | `string\|null` | `null` | Logo image URL (shown instead of text) |
| `sticky` | `bool` | `true` | Fixes header to top of viewport |
| `shadow` | `bool` | `true` | Renders `shadow-sm` |
| `height` | `string` | `h-16` | Tailwind height class |
| `sidebar-id` | `string` | `''` | If set, renders a hamburger button that toggles this sidebar |

---

## Sidebar

Collapsible side navigation panel. Toggle via Header `sidebar-id` or Alpine's `$dispatch('toggle-sidebar')`.

```blade
<x-tallui-sidebar
    id="main-sidebar"
    position="left"
    width="w-64"
    :overlay="true"
    :persistent="false"
    collapse-breakpoint="lg"
    header="Main Menu"
    footer="v1.0"
>
    <x-tallui-menu-item label="Dashboard"  icon="o-home"       :link="route('dashboard')" :active="request()->routeIs('dashboard')" />
    <x-tallui-menu-item label="Invoices"   icon="o-document-text" :link="route('invoices')" />
    <x-tallui-menu-item label="Reports"    icon="o-chart-bar"  :link="route('reports')" />
    <x-tallui-menu-item :separator="true" />
    <x-tallui-menu-item label="Settings"   icon="o-cog"        :link="route('settings')" />
</x-tallui-sidebar>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `id` | `string` | `sidebar` | Alpine `x-data` id for toggling |
| `position` | `string` | `left` | `left` \| `right` |
| `width` | `string` | `w-64` | Tailwind width class |
| `overlay` | `bool` | `true` | Dark overlay behind drawer when open |
| `persistent` | `bool` | `false` | Always visible — no toggle on large screens |
| `collapse-breakpoint` | `string` | `lg` | Tailwind breakpoint at which sidebar shows persistently |
| `header` | `string\|null` | `null` | Text above nav items |
| `footer` | `string\|null` | `null` | Text below nav items |

---

## PageHeader

Page-level title block with optional breadcrumbs, subtitle, icon, and action buttons.

```blade
<x-tallui-page-header
    title="Invoices"
    subtitle="Manage your customer invoices"
    icon="heroicon-o-document-text"
    :separator="true"
>
    <x-slot:breadcrumbs>
        <x-tallui-breadcrumb :links="[
            ['label' => 'Home',     'href' => route('dashboard')],
            ['label' => 'Invoices'],
        ]" />
    </x-slot:breadcrumbs>

    <x-slot:actions>
        <x-tallui-button label="New Invoice" icon="heroicon-o-plus" class="btn-primary" wire:click="create" />
    </x-slot:actions>
</x-tallui-page-header>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `title` | `string` | `''` | Main heading |
| `subtitle` | `string\|null` | `null` | Secondary text beneath title |
| `icon` | `string\|null` | `null` | Heroicon name |
| `separator` | `bool` | `true` | Renders a horizontal rule below the header |

| Slot | Description |
| --- | --- |
| `breadcrumbs` | Breadcrumb trail (rendered above the title) |
| `actions` | Button(s) aligned to the right |

---

## Card

Content container with optional header row (title, subtitle, icon, action slot) and footer slot.

```blade
<x-tallui-card
    title="Monthly Revenue"
    subtitle="April 2026"
    icon="heroicon-o-banknotes"
    :shadow="true"
    :bordered="false"
    padding="normal"
>
    <x-slot:actions>
        <x-tallui-button icon="heroicon-o-arrow-path" class="btn-ghost btn-sm" wire:click="refresh" />
    </x-slot:actions>

    {{-- body --}}
    <p class="text-3xl font-bold">৳ 51,00,000</p>

    <x-slot:footer>
        <span class="text-sm text-base-content/50">Updated just now</span>
    </x-slot:footer>
</x-tallui-card>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `title` | `string\|null` | `null` | Card header title |
| `subtitle` | `string\|null` | `null` | Secondary text next to title |
| `icon` | `string\|null` | `null` | Heroicon in the header row |
| `shadow` | `bool` | `true` | `shadow-md` |
| `bordered` | `bool` | `false` | `card-bordered` class |
| `padding` | `string` | `normal` | `none` \| `compact` \| `normal` \| `loose` |

| Slot | Description |
| --- | --- |
| `actions` | Buttons aligned right in the header row |
| `footer` | Content rendered below the main body |

---

## EmptyState

Full-width empty state block shown when a list has no results. Default slot is for action buttons.

```blade
<x-tallui-empty-state
    title="No invoices yet"
    description="Create your first invoice to get started."
    icon="heroicon-o-document-text"
    size="md"
>
    <x-tallui-button label="Create Invoice" icon="heroicon-o-plus" class="btn-primary" wire:click="create" />
</x-tallui-empty-state>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `title` | `string` | `No results found` | Bold heading |
| `description` | `string\|null` | `null` | Supporting text |
| `icon` | `string` | `o-inbox` | Heroicon name |
| `size` | `string` | `md` | `sm` \| `md` \| `lg` (controls vertical padding) |
