# TallUI — Laravel UI Components Library

[![Latest Version on Packagist](https://img.shields.io/packagist/v/centrex/tallui.svg?style=flat-square)](https://packagist.org/packages/centrex/tallui)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/centrex/tallui/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/centrex/tallui/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/centrex/tallui/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/centrex/tallui/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/centrex/tallui?style=flat-square)](https://packagist.org/packages/centrex/tallui)

Reusable Blade and Livewire UI components built on **DaisyUI + Alpine.js**. Includes layout helpers, interactive components, a full-featured DataTable with search/sort/export, and ApexCharts-powered chart components.

## Installation

```bash
composer require centrex/tallui
php artisan vendor:publish --tag="tallui-config"
```

Requires: `livewire/livewire ^3`, `blade-ui-kit/blade-heroicons` (for icons).

## Configuration

```php
// config/tallui.php
'prefix' => 'tallui',   // component prefix; '' → <x-button />, 'tallui' → <x-tallui-button />
```

Run `php artisan view:clear` after changing the prefix.

## Blade Components

All components pass through `$attributes`, so standard HTML attributes and DaisyUI classes merge cleanly.

### Layout

```blade
{{-- Page header with optional breadcrumbs and action slot --}}
<x-tallui-page-header title="Customers" subtitle="Manage your list" icon="o-users">
    <x-slot:breadcrumbs>
        <x-tallui-breadcrumb :links="[['label' => 'Home', 'href' => '/'], ['label' => 'Customers']]" />
    </x-slot:breadcrumbs>
    <x-slot:actions>
        <x-tallui-button label="New" icon="o-plus" class="btn-primary" wire:click="create" />
    </x-slot:actions>
</x-tallui-page-header>

{{-- Card with optional title, icon, actions slot, and footer slot --}}
<x-tallui-card title="Revenue" subtitle="Last 30 days" icon="o-chart-bar" padding="normal">
    <x-slot:actions>
        <x-tallui-button icon="o-arrow-path" class="btn-ghost btn-sm" />
    </x-slot:actions>
    <p class="text-3xl font-bold">৳ 1,24,000</p>
    <x-slot:footer>Updated just now</x-slot:footer>
</x-tallui-card>
```

`padding` values: `none | compact | normal | loose`.

### Stats

```blade
<div class="stats shadow w-full">
    <x-tallui-stat title="Revenue" value="৳1,24,000" icon="o-banknotes"
        change="+12%" change-type="up" desc="vs last month" />
    <x-tallui-stat title="Overdue" value="7" icon="o-exclamation-circle"
        icon-color="text-error" change="-2" change-type="down" />
</div>
```

`change-type`: `up | down | neutral`.

### Badges

```blade
<x-tallui-badge type="success">Active</x-tallui-badge>
<x-tallui-badge type="warning" size="sm">Pending</x-tallui-badge>
<x-tallui-badge color="outline">Draft</x-tallui-badge>
```

Types/colors: `success | error | warning | info | primary | secondary | accent | ghost | outline | neutral`.
Sizes: `xs | sm | md | lg`.

### Alerts

```blade
<x-tallui-alert type="warning" title="Action required" :dismissible="true">
    Your subscription expires in 3 days.
</x-tallui-alert>
```

Types: `info | success | warning | error`. Icon is auto-selected per type.

### Buttons

```blade
{{-- Link with wire:navigate --}}
<x-tallui-button label="Edit" icon="o-pencil" :link="route('orders.edit', $order)" class="btn-ghost btn-sm" />

{{-- Loading spinner tied to wire:click --}}
<x-tallui-button label="Save" :spinner="1" wire:click="save" class="btn-primary" />

{{-- Responsive: hides label on mobile --}}
<x-tallui-button label="Delete" icon="o-trash" :responsive="true" class="btn-error" />

{{-- With tooltip --}}
<x-tallui-button icon="o-trash" tooltip="Delete record" class="btn-ghost" />
```

### Empty state

```blade
<x-tallui-empty-state
    title="No invoices yet"
    description="Create your first invoice to get started."
    icon="heroicon-o-document-text"
    size="md"
>
    <x-tallui-button label="Create Invoice" icon="o-plus" class="btn-primary" />
</x-tallui-empty-state>
```

Sizes: `sm | md | lg`.

### Notifications

Place once in your layout. Reads `session('success' | 'error' | 'warning' | 'info' | 'message')` automatically, and listens for Livewire-dispatched events:

```blade
{{-- layout.blade.php --}}
<x-tallui-notification position="top-right" :timeout="4000" />
```

```php
// In any Livewire component
$this->dispatch('notify', type: 'success', message: 'Saved!');
$this->dispatch('notify', type: 'error',   message: 'Something went wrong.');
```

Positions: `top-right | top-left | bottom-right | bottom-left | top-center`.

### Form components

```blade
<x-tallui-input wire:model="name" label="Name" />
<x-tallui-select wire:model="status" :options="$statuses" />
<x-tallui-textarea wire:model="description" rows="4" />
<x-tallui-checkbox wire:model="agree" label="I agree to the terms" />
<x-tallui-toggle wire:model="enabled" label="Active" />
<x-tallui-date-picker wire:model="date" />
<x-tallui-text-editor wire:model="body" />
```

### Other components

`accordion`, `avatar`, `breadcrumb`, `calendar`, `carousel`, `drawer`, `group`, `image-gallery`, `loading`, `menu`, `modal`, `popover`, `progress`, `rating`, `spotlight`, `steps`, `swap`, `tab`, `tags`, `theme-toggle`, `timeline`, `toast`.

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
            Column::make('Actions')->actions(),
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

## Charts

```blade
<livewire:tallui-line-chart :series="$series" :categories="$months" title="Revenue" />
<livewire:tallui-bar-chart  :series="$series" :categories="$months" />
<livewire:tallui-pie-chart  :series="$values"  :labels="$labels" />
<livewire:tallui-area-chart :series="$series" :categories="$months" />
```

Powered by ApexCharts (loaded via CDN). CDN URL is configurable in `config/tallui.php`.

## Testing

```bash
composer test        # full suite
composer test:unit   # pest only
composer test:types  # phpstan
composer lint        # pint
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [centrex](https://github.com/centrex)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
