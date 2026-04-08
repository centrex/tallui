# TallUI — Laravel UI Components Library

[![Latest Version on Packagist](https://img.shields.io/packagist/v/centrex/tallui.svg?style=flat-square)](https://packagist.org/packages/centrex/tallui)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/centrex/tallui/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/centrex/tallui/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/centrex/tallui/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/centrex/tallui/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/centrex/tallui?style=flat-square)](https://packagist.org/packages/centrex/tallui)

Reusable Blade and Livewire UI components built on **DaisyUI + Alpine.js**. Includes layout helpers, interactive components, a full-featured DataTable with search/sort/export, and ApexCharts-powered chart components.

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Blade Components](#blade-components)
  - [Layout — Header, Page Header, Card, Stats](#layout)
  - [Navigation — Sidebar, Breadcrumb, Menu, Tabs](#navigation)
  - [Overlays — Modal, Dialog, Drawer](#overlays)
  - [Feedback — Alert, Badge, Notification, Empty State, Loading, Progress](#feedback)
  - [Buttons & Icons](#buttons--icons)
  - [Display — Accordion, Collapse, Carousel, Image Gallery, Image Library, Timeline, Steps, Avatar, Rating, Swap, Tags](#display)
  - [Form Components](#form-components)
- [Livewire DataTable](#livewire-datatable)
- [Charts](#charts)
- [Performance Blade Directives](#performance-blade-directives)
- [Testing](#testing)

---

## Installation

```bash
composer require centrex/tallui
php artisan vendor:publish --tag="tallui-config"
```

**Requires:** `livewire/livewire ^3`, `blade-ui-kit/blade-heroicons` (for icons).

---

## Configuration

```php
// config/tallui.php
'prefix' => 'tallui',   // '' → <x-button />, 'tallui' → <x-tallui-button />
```

Run `php artisan view:clear` after changing the prefix.

---

## Blade Components

All components pass through `$attributes`, so HTML attributes and DaisyUI/Tailwind classes merge cleanly.

---

### Layout

#### Header

Sticky application navbar. Integrates with `<x-tallui-sidebar>` via `sidebar-id`.

```blade
<x-tallui-header
    brand="MyApp"
    brand-href="/"
    brand-logo="/img/logo.svg"
    sidebar-id="main-sidebar"
    :sticky="true"
>
    <x-slot:center>
        <a href="/dashboard" class="btn btn-ghost btn-sm">Dashboard</a>
        <a href="/reports"   class="btn btn-ghost btn-sm">Reports</a>
    </x-slot:center>

    <x-slot:actions>
        <x-tallui-theme-toggle />
        <x-tallui-button icon="heroicon-o-bell" class="btn-ghost btn-circle" />
        <div class="avatar"><div class="w-8 rounded-full"><img src="/avatar.jpg" /></div></div>
    </x-slot:actions>
</x-tallui-header>
```

| Prop | Default | Description |
|---|---|---|
| `brand` | `null` | Brand name text |
| `brand-href` | `/` | Brand link URL |
| `brand-logo` | `null` | Logo image URL |
| `sticky` | `true` | Stick to top of viewport |
| `shadow` | `true` | Show bottom shadow |
| `height` | `h-16` | Tailwind height class |
| `sidebar-id` | `''` | Dispatches `toggle-sidebar` event when hamburger clicked |

Named slots: `brandSlot`, `center`, `actions`.

---

#### Sidebar

Slide-in navigation panel. Toggleable via Alpine events or the Header's hamburger button.

```blade
{{-- Layout wrapper --}}
<div class="flex min-h-screen">
    <x-tallui-sidebar
        id="main-sidebar"
        position="left"
        width="w-64"
        :overlay="true"
        :persistent="true"
        header="Navigation"
    >
        <nav class="menu menu-md px-2">
            <li><a href="/dashboard">Dashboard</a></li>
            <li><a href="/users">Users</a></li>
        </nav>

        <x-slot:footerSlot>
            <x-tallui-button label="Logout" icon="heroicon-o-arrow-right-on-rectangle" class="btn-ghost w-full" />
        </x-slot:footerSlot>
    </x-tallui-sidebar>

    <main class="flex-1 p-6">...</main>
</div>

{{-- Open/close from anywhere --}}
<button @click="$dispatch('toggle-sidebar', 'main-sidebar')">Menu</button>
```

| Prop | Default | Description |
|---|---|---|
| `id` | `sidebar` | Unique ID; used in Alpine events |
| `position` | `left` | `left` or `right` |
| `width` | `w-64` | Tailwind width class |
| `overlay` | `true` | Dark backdrop behind sidebar |
| `persistent` | `false` | Always visible on `collapse-breakpoint`+ screens |
| `collapse-breakpoint` | `lg` | Breakpoint where sidebar becomes always-visible |
| `header` | `null` | Header text (use `headerSlot` for custom content) |

Named slots: `headerSlot`, `footerSlot`.

---

#### Page Header

```blade
<x-tallui-page-header title="Customers" subtitle="Manage your list" icon="o-users">
    <x-slot:breadcrumbs>
        <x-tallui-breadcrumb :links="[['label' => 'Home', 'href' => '/'], ['label' => 'Customers']]" />
    </x-slot:breadcrumbs>
    <x-slot:actions>
        <x-tallui-button label="New" icon="heroicon-o-plus" class="btn-primary" wire:click="create" />
    </x-slot:actions>
</x-tallui-page-header>
```

---

#### Card

```blade
<x-tallui-card title="Revenue" subtitle="Last 30 days" icon="o-chart-bar" :shadow="true" padding="normal">
    <x-slot:actions>
        <x-tallui-button icon="o-arrow-path" class="btn-ghost btn-sm" />
    </x-slot:actions>
    <p class="text-3xl font-bold">৳ 1,24,000</p>
    <x-slot:footer>
        <span class="text-sm text-base-content/50">Updated just now</span>
    </x-slot:footer>
</x-tallui-card>
```

`padding`: `none | compact | normal | loose`.

---

#### Stats

```blade
<div class="stats shadow w-full">
    <x-tallui-stat title="Revenue" value="৳1,24,000" icon="o-banknotes"
        change="+12%" change-type="up" desc="vs last month" />
    <x-tallui-stat title="Overdue" value="7" icon="o-exclamation-circle"
        icon-color="text-error" change="-2" change-type="down" />
</div>
```

`change-type`: `up | down | neutral`.

---

### Navigation

#### Breadcrumb

```blade
<x-tallui-breadcrumb :links="[
    ['label' => 'Home',     'href' => '/'],
    ['label' => 'Users',    'href' => '/users'],
    ['label' => 'John Doe'],  {{-- no href = current page --}}
]" />
```

#### Menu

```blade
<x-tallui-menu>
    <li><a href="/profile">Profile</a></li>
    <li><a href="/settings">Settings</a></li>
    <li><hr class="my-1 border-base-200" /></li>
    <li><a href="/logout">Logout</a></li>
</x-tallui-menu>
```

#### Tabs

```blade
<x-tallui-tab :tabs="['Overview', 'Analytics', 'Reports']" active="Overview" />
```

---

### Overlays

#### Modal

Full-featured modal with backdrop, keyboard close, and named slots.

```blade
<x-tallui-modal id="confirm-delete" title="Delete user?" icon="heroicon-o-trash" icon-color="text-error">
    <x-slot:trigger>
        <x-tallui-button label="Delete" class="btn-error" />
    </x-slot:trigger>

    <p>Are you sure you want to delete this user? This action cannot be undone.</p>

    <x-slot:footer>
        <button class="btn" @click="$dispatch('close-modal', 'confirm-delete')">Cancel</button>
        <button class="btn btn-error" wire:click="delete">Yes, delete</button>
    </x-slot:footer>
</x-tallui-modal>
```

Open/close from anywhere: `$dispatch('open-modal', 'confirm-delete')`.

| Prop | Default | Options |
|---|---|---|
| `id` | `modal` | Unique ID |
| `size` | `md` | `sm \| md \| lg \| xl \| full` |
| `closeable` | `true` | Show × button + backdrop click to close |

---

#### Dialog

Lightweight centred confirmation dialog. Auto-selects icon and colour per `type`.

```blade
<x-tallui-dialog
    id="delete-confirm"
    type="error"
    title="Delete this record?"
    size="sm"
>
    <x-slot:trigger>
        <x-tallui-button label="Delete" class="btn-error btn-sm" />
    </x-slot:trigger>

    <p>This action is permanent and cannot be reversed.</p>

    <x-slot:footer>
        <button class="btn btn-ghost" @click="$dispatch('close-dialog', 'delete-confirm')">Cancel</button>
        <button class="btn btn-error" wire:click="delete">Delete</button>
    </x-slot:footer>
</x-tallui-dialog>
```

Open programmatically: `$dispatch('open-dialog', 'delete-confirm')`.

| `type` | Icon | Colour |
|---|---|---|
| `info` | information-circle | text-info |
| `success` | check-circle | text-success |
| `warning` | exclamation-triangle | text-warning |
| `error` | x-circle | text-error |
| `confirm` | question-mark-circle | text-primary |

---

#### Drawer

```blade
<x-tallui-drawer id="cart" position="right" title="Shopping Cart">
    <x-slot:trigger>
        <x-tallui-button icon="heroicon-o-shopping-cart" class="btn-ghost" />
    </x-slot:trigger>
    {{-- Drawer body --}}
</x-tallui-drawer>
```

---

### Feedback

#### Alerts

```blade
<x-tallui-alert type="warning" title="Action required" :dismissible="true">
    Your subscription expires in 3 days.
</x-tallui-alert>
```

Types: `info | success | warning | error`. Icon auto-selected per type.

#### Badges

```blade
<x-tallui-badge type="success">Active</x-tallui-badge>
<x-tallui-badge type="warning" size="sm">Pending</x-tallui-badge>
<x-tallui-badge color="outline">Draft</x-tallui-badge>
```

Types/colors: `success | error | warning | info | primary | secondary | accent | ghost | outline | neutral`.
Sizes: `xs | sm | md | lg`.

#### Notifications

Place once in your layout. Reads session flash keys and listens for Livewire events:

```blade
{{-- layout.blade.php --}}
<x-tallui-notification position="top-right" :timeout="4000" />
```

```php
// In any Livewire component
$this->dispatch('notify', type: 'success', message: 'Saved!');
$this->dispatch('notify', type: 'error',   message: 'Something went wrong.');
```

Session keys auto-displayed: `success`, `error`, `warning`, `info`, `message`.
Positions: `top-right | top-left | bottom-right | bottom-left | top-center`.

#### Empty State

```blade
<x-tallui-empty-state
    title="No invoices yet"
    description="Create your first invoice to get started."
    icon="heroicon-o-document-text"
    size="md"
>
    <x-tallui-button label="Create Invoice" icon="heroicon-o-plus" class="btn-primary" />
</x-tallui-empty-state>
```

Sizes: `sm | md | lg`.

#### Loading & Progress

```blade
<x-tallui-loading />
<x-tallui-progress value="72" color="primary" size="sm" />
```

---

### Buttons & Icons

```blade
{{-- Link with wire:navigate --}}
<x-tallui-button label="Edit" icon="o-pencil" :link="route('orders.edit', $order)" class="btn-ghost btn-sm" />

{{-- Loading spinner tied to wire:click --}}
<x-tallui-button label="Save" :spinner="1" wire:click="save" class="btn-primary" />

{{-- Responsive: hides label on mobile --}}
<x-tallui-button label="Delete" icon="o-trash" :responsive="true" class="btn-error" />

{{-- Tooltip --}}
<x-tallui-button icon="o-question-mark-circle" tooltip="Help" class="btn-ghost" />

{{-- Heroicon --}}
<x-tallui-icon name="heroicon-o-star" class="w-6 h-6 text-warning" />
```

---

### Display

#### Accordion

```blade
<x-tallui-accordion name="faq" title="What is TallUI?" variant="arrow" :open="false">
    TallUI is a Laravel UI component library built on DaisyUI and Alpine.js.
</x-tallui-accordion>
```

Variants: `arrow | plus`.

---

#### Collapse

Single collapsible panel with smooth Alpine.js transitions. Lighter than Accordion for standalone use.

```blade
<x-tallui-collapse title="Advanced settings" :bordered="true" variant="arrow">
    <div class="flex flex-col gap-3 pt-2">
        <x-tallui-toggle label="Enable debug mode" />
        <x-tallui-toggle label="Show verbose logs" />
    </div>
</x-tallui-collapse>
```

| Prop | Default | Description |
|---|---|---|
| `title` | `''` | Header text |
| `open` | `false` | Expanded by default |
| `bordered` | `false` | Border instead of filled background |
| `variant` | `arrow` | `arrow \| plus \| none` |

---

#### Image Gallery

Read-only image grid with built-in lightbox:

```blade
<x-tallui-image-gallery
    :images="[
        ['src' => '/img/1.jpg', 'alt' => 'Photo 1', 'caption' => 'Sunrise'],
        ['src' => '/img/2.jpg', 'alt' => 'Photo 2'],
    ]"
    :columns="3"
    :lightbox="true"
    fit="cover"
    height="h-48"
/>
```

---

#### Image Library

Selectable image picker with lightbox and form submission support. Double-click opens lightbox; single-click selects.

```blade
<x-tallui-image-library
    :images="$mediaLibrary"
    :multiple="true"
    :selected="$selectedIds"
    name="media_ids"
    :columns="4"
    fit="cover"
    height="h-32"
/>
```

| Prop | Default | Description |
|---|---|---|
| `images` | `[]` | Array of `['src', 'alt'?, 'caption'?, 'id'?]` |
| `multiple` | `false` | Allow multiple selections |
| `selected` | `[]` | Pre-selected ids/srcs |
| `name` | `null` | If set, renders hidden inputs for form posting |
| `selectable` | `true` | Enable selection UI |
| `columns` | `4` | Grid column count (sm: 2, sm+: columns) |

---

#### Other Display Components

```blade
{{-- Carousel --}}
<x-tallui-carousel :slides="$slides" :autoplay="true" :interval="3000" />

{{-- Timeline --}}
<x-tallui-timeline :items="$events" />

{{-- Steps --}}
<x-tallui-steps :steps="['Cart', 'Shipping', 'Payment', 'Confirm']" :current="2" />

{{-- Rating --}}
<x-tallui-rating name="score" :value="3" :max="5" />

{{-- Avatar --}}
<x-tallui-avatar src="/img/user.jpg" size="md" />

{{-- Tags --}}
<x-tallui-tags :items="['Laravel', 'Livewire', 'Alpine']" />

{{-- Swap (toggle icon) --}}
<x-tallui-swap on-icon="heroicon-o-sun" off-icon="heroicon-o-moon" />

{{-- Theme Toggle --}}
<x-tallui-theme-toggle />

{{-- Popover --}}
<x-tallui-popover content="Helpful info">
    <x-tallui-button icon="heroicon-o-information-circle" class="btn-ghost btn-xs" />
</x-tallui-popover>

{{-- Spotlight search --}}
<x-tallui-spotlight />
```

---

### Form Components

#### Standard Inputs

```blade
<x-tallui-input      name="email" label="Email" type="email" wire:model="email" :required="true" />
<x-tallui-textarea   name="bio" label="Bio" :rows="4" wire:model="bio" />
<x-tallui-select     name="role" label="Role" :options="$roles" wire:model="role" />
<x-tallui-checkbox   name="agree" label="I agree to the terms" wire:model="agree" />
<x-tallui-radio      name="gender" label="Male" value="male" wire:model="gender" />
<x-tallui-toggle     name="active" label="Active" wire:model="active" />
<x-tallui-date-picker name="birthday" label="Birthday" wire:model="birthday" />
<x-tallui-form-group label="Notes" error="Required" helper="Max 500 chars">
    <x-tallui-textarea name="notes" />
</x-tallui-form-group>
```

---

#### Range Slider

```blade
<x-tallui-range
    name="budget"
    label="Budget"
    :min="0"
    :max="10000"
    :step="100"
    :value="5000"
    color="primary"
    :show-value="true"
    :show-steps="true"
/>
```

---

#### Rich Text Editor

Content-editable rich text editor with formatting toolbar (bold, italic, underline, lists, headings). Output stored in a hidden `<input>` for form submission.

```blade
<x-tallui-text-editor
    name="content"
    label="Article body"
    :value="old('content', $post->content)"
    placeholder="Start writing…"
    :rows="10"
    :required="true"
/>
```

---

#### Choices (Multi-Select)

Searchable multi-select with tag badges. Outputs hidden inputs for form submission.

```blade
<x-tallui-choices
    name="tags[]"
    label="Tags"
    :options="['laravel' => 'Laravel', 'vue' => 'Vue.js', 'alpine' => 'Alpine.js']"
    :selected="['laravel']"
    :multiple="true"
    :searchable="true"
    placeholder="Select tags…"
/>
```

Options can also be a list of `['value' => '...', 'label' => '...']` arrays.

| Prop | Default | Description |
|---|---|---|
| `options` | `[]` | Flat associative array or `[{value, label}]` list |
| `selected` | `[]` | Pre-selected values |
| `multiple` | `true` | Allow multiple selections |
| `searchable` | `true` | Show search input in dropdown |

---

#### File Upload (Drag & Drop)

Drag-and-drop zone with client-side file size validation and image preview thumbnails.

```blade
<x-tallui-file-upload
    name="attachments"
    label="Attachments"
    :multiple="true"
    accept="image/*,.pdf"
    :max-size-mb="5"
    :preview="true"
    upload-text="Drop files here or click to upload"
/>
```

| Prop | Default | Description |
|---|---|---|
| `multiple` | `false` | Accept multiple files |
| `accept` | `null` | MIME/extension filter (e.g. `image/*,.pdf`) |
| `max-size-mb` | `10` | Client-side size limit per file |
| `preview` | `true` | Show image thumbnails and file cards below zone |

> Note: This is a pure client-side component. For server-side upload use Livewire's `wire:model` with the standard `<x-tallui-file>` input or Laravel's file storage.

---

#### File Input (Simple)

```blade
<x-tallui-file
    name="avatar"
    label="Profile Photo"
    accept="image/*"
    :required="true"
/>
```

---

#### PIN / OTP Input

Auto-advancing digit inputs with paste support, backspace navigation, and masked mode.

```blade
<x-tallui-pin
    name="otp"
    label="Verification code"
    :length="6"
    :masked="false"
    :numeric="true"
    size="md"
    helper="Check your email for the 6-digit code."
/>
```

| Prop | Default | Description |
|---|---|---|
| `length` | `6` | Number of digit inputs |
| `masked` | `false` | `password`-type inputs (dots) |
| `numeric` | `true` | Only allow digits; set `false` for alphanumeric OTPs |
| `size` | `md` | `sm \| md \| lg` |

Behaviour: typing auto-advances to the next cell, Backspace moves back, paste distributes across all cells, a visual separator is inserted at the midpoint for 6+ digit codes.

---

## Livewire DataTable

Extend `DataTable`, override `columns()` and `query()`:

```php
use Centrex\TallUi\DataTable\Column;
use Centrex\TallUi\Livewire\DataTable;
use Illuminate\Database\Eloquent\Builder;

class CustomerTable extends DataTable
{
    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Email', 'email')->searchable(),
            Column::make('Balance', 'outstanding_balance')->sortable(),
            Column::make('Status', 'status')->badge('neutral', ['active' => 'success', 'inactive' => 'error']),
            Column::make('Actions')->actions([...]),
        ];
    }

    public function query(): Builder
    {
        return Customer::query();
    }
}
```

```blade
<livewire:tallui-data-table />
```

Features: URL-synced search/sort/page (`#[Url]`), per-page selector, row selection, CSV export (chunked + UTF-8 BOM), optional result caching via `$cacheTtl`.

### Responsive DataTable

Automatically switches between a desktop table and a mobile card stack at the `md` breakpoint. Change the breakpoint by overriding `$mobileBreakpoint`:

```php
class CustomerTable extends DataTable
{
    public string $mobileBreakpoint = 'lg';   // cards below 1024 px
    // public string $mobileBreakpoint = '';  // disable card stack
}
```

Hide columns at smaller widths:

```php
Column::make('Name',   'name')->searchable()->sortable(),   // always visible
Column::make('Email',  'email')->hideOnMobile(),             // hidden below md
Column::make('Phone',  'phone')->visibleFrom('lg'),          // hidden below lg
Column::make('Tax ID', 'tax_id')->visibleFrom('xl'),         // hidden below xl
```

---

## Charts

All charts are Livewire components powered by [ApexCharts](https://apexcharts.com) (loaded via CDN, configurable in `config/tallui.php`).

### Standard Charts

```blade
<livewire:tallui-line-chart  :series="$series" :categories="$months" title="Revenue" />
<livewire:tallui-bar-chart   :series="$series" :categories="$months" :horizontal="false" />
<livewire:tallui-area-chart  :series="$series" :categories="$months" :smooth="true" />
<livewire:tallui-pie-chart   :series="$values" :categories="$labels" />
<livewire:tallui-pie-chart   :series="$values" :categories="$labels" :donut="true" />
```

Common props (all charts): `title`, `subtitle`, `height` (px, default 350), `theme` (`light|dark`), `poll` (ms, 0 = disabled), `data-provider` (FQCN of `ChartDataProvider`).

---

### Mixed (Combo) Chart

Combines bar, line, and area series in one canvas. Each series must include a `type` key.

```blade
<livewire:tallui-mixed-chart :series="$series" :categories="$months" title="Revenue vs Trend" />
```

```php
$series = [
    ['name' => 'Revenue',  'type' => 'bar',  'data' => [50000, 70000, 80000, 60000]],
    ['name' => 'Trend',    'type' => 'line', 'data' => [55000, 65000, 75000, 70000]],
    ['name' => 'Forecast', 'type' => 'area', 'data' => [48000, 68000, 78000, 72000]],
];
$months = ['Jan', 'Feb', 'Mar', 'Apr'];
```

---

### Treemap Chart

Hierarchical tile visualisation. Series uses `{x, y}` data pairs; multiple series = multiple colour groups.

```blade
<livewire:tallui-treemap-chart :series="$series" title="Product Sales" />
```

```php
$series = [
    [
        'name' => 'Electronics',
        'data' => [
            ['x' => 'Phones',   'y' => 90],
            ['x' => 'Laptops',  'y' => 75],
            ['x' => 'Tablets',  'y' => 40],
        ],
    ],
    [
        'name' => 'Clothing',
        'data' => [
            ['x' => 'Shirts',   'y' => 55],
            ['x' => 'Shoes',    'y' => 38],
        ],
    ],
];
```

Props: `:distributed="true"` (each tile its own colour), `:enable-shades="true"`.

---

### Radial Bar Chart

Gauge-style circular arcs. Each arc represents one percentage value. Shows averaged total in the centre.

```blade
<livewire:tallui-radial-bar-chart
    :series="[75, 55, 90]"
    :categories="['CPU', 'Memory', 'Disk']"
    title="Server Usage"
    start-angle="-135"
    end-angle="135"
/>
```

| Prop | Default | Description |
|---|---|---|
| `series` | `[]` | Flat array of percentages (0–100) |
| `categories` | `[]` | Arc labels, one per value |
| `start-angle` | `-135` | Start angle in degrees |
| `end-angle` | `135` | End angle in degrees |
| `hollow` | `true` | Donut-style hollow centre |
| `track` | `''` | Custom track background colour |

---

### Radar Chart

Spider/web chart for multi-dimensional comparison. Supports multiple series on the same grid.

```blade
<livewire:tallui-radar-chart
    :series="$series"
    :categories="['Speed', 'Power', 'Range', 'Efficiency', 'Comfort', 'Safety']"
    title="Product Comparison"
/>
```

```php
$series = [
    ['name' => 'Model A', 'data' => [80, 50, 30, 40, 100, 20]],
    ['name' => 'Model B', 'data' => [60, 85, 70, 55,  75, 60]],
];
```

---

### Polar Area Chart

Proportional sectors that also extend outward by value — like a pie chart with a size dimension. Series is a flat array; categories are the sector labels.

```blade
<livewire:tallui-polar-area-chart
    :series="[42, 18, 35, 27, 14]"
    :categories="['North', 'South', 'East', 'West', 'Central']"
    title="Regional Distribution"
/>
```

---

### Range Area Chart

Shaded band between a low and a high value per x-point. Ideal for confidence intervals, temperature ranges, and min/max visualisations.

```blade
<livewire:tallui-range-area-chart :series="$series" title="Temperature Range" />
```

```php
$series = [
    [
        'name' => 'Temperature',
        'data' => [
            ['x' => 'Jan', 'y' => [2,  12]],
            ['x' => 'Feb', 'y' => [3,  15]],
            ['x' => 'Mar', 'y' => [8,  22]],
            ['x' => 'Apr', 'y' => [13, 28]],
        ],
    ],
];
```

Props: `:smooth="true"` — smooth or straight stroke.

---

### Data Providers

For charts fed from a service or cache, implement `ChartDataProvider`:

```php
use Centrex\TallUi\Contracts\ChartDataProvider;

class RevenueDataProvider implements ChartDataProvider
{
    public function getData(): array
    {
        return [
            'series'     => [['name' => 'Revenue', 'data' => [50, 70, 80]]],
            'categories' => ['Jan', 'Feb', 'Mar'],
        ];
    }
}
```

```blade
<livewire:tallui-line-chart data-provider="App\Charts\RevenueDataProvider" />
```

---

## Performance Blade Directives

Registered automatically by the service provider.

### `@pushonce` / `@endpushonce`

Push a block to a named stack **exactly once** per request — prevents duplicate `<script>` or `<link>` tags when a component renders multiple times on one page.

```blade
@pushonce('scripts', 'my-lib')
    <script src="/vendor/my-lib.js"></script>
@endpushonce
```

The second argument is the deduplication key. Omit it to use the stack name as the key.

---

### `@memoize` / `@endmemoize`

Render a Blade block **once per request**, then replay the cached HTML on every subsequent call. Use inside loops for expensive sub-views (icon sets, nav links, etc.).

```blade
@foreach($rows as $row)
    @memoize('shared-icon-set')
        <x-tallui-icon name="heroicon-o-check" class="w-4 h-4" />
    @endmemoize
@endforeach
```

The block is rendered on the first encounter; all subsequent calls output the same cached string instantly.

---

### `@lazy` / `@endlazy`

Defer Alpine.js initialisation until the wrapped content **scrolls near the viewport** (uses Alpine `x-intersect`). Reduces the JS work on initial page load for below-the-fold components.

```blade
@lazy
    <livewire:tallui-bar-chart :series="$series" />
@endlazy
```

Requires Alpine's `x-intersect` plugin (bundled with Livewire 3 / Alpine 3.x).

---

### `@styleonce` / `@endstyleonce`

Push a `<style>` block to the `styles` stack at most once, regardless of how many times the component renders.

```blade
@styleonce('pin-input')
    <style>
        .pin-cell { letter-spacing: 0.5em; }
    </style>
@endstyleonce
```

---

### `@scriptonce` / `@endscriptonce`

Push a `<script>` block to the `scripts` stack at most once.

```blade
@scriptonce('my-init')
    <script>
        window.myLib = { version: '1.0' };
    </script>
@endscriptonce
```

---

## Testing

```bash
composer test        # full suite: rector dry-run, pint check, phpstan, pest
composer test:unit   # pest tests only
composer test:types  # phpstan static analysis
composer lint        # apply pint formatting
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [centrex](https://github.com/centrex)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
