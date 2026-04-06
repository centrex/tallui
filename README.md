# TallUI — Laravel UI Components Library

[![Latest Version on Packagist](https://img.shields.io/packagist/v/centrex/tallui.svg?style=flat-square)](https://packagist.org/packages/centrex/tallui)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/centrex/tallui/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/centrex/tallui/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/centrex/tallui/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/centrex/tallui/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/centrex/tallui?style=flat-square)](https://packagist.org/packages/centrex/tallui)

A full-featured UI component library for Laravel + Livewire + Tailwind CSS 4 (DaisyUI 5). Ships server-driven **data tables**, reactive **form components**, and live **chart components** — all with async data sync via Livewire.

## Stack Requirements

| Dependency | Version |
| --- | --- |
| PHP | ^8.2 |
| Laravel | ^11 \| ^12 \| ^13 |
| Livewire | ^3 \| ^4 |
| Tailwind CSS | ^3 \| ^4 |
| DaisyUI | ^4 (with TW3) \| ^5 (with TW4) |
| Alpine.js | included with Livewire 3+ |
| ApexCharts | CDN auto-injected |

---

## Contents

- [TallUI — Laravel UI Components Library](#tallui--laravel-ui-components-library)
  - [Stack Requirements](#stack-requirements)
  - [Contents](#contents)
  - [Installation](#installation)
    - [Tailwind CSS 4 + DaisyUI 5](#tailwind-css-4--daisyui-5)
    - [Tailwind CSS 3 + DaisyUI 4](#tailwind-css-3--daisyui-4)
    - [Layout](#layout)
  - [Configuration](#configuration)
  - [Form Components](#form-components)
    - [FormGroup](#formgroup)
    - [Input](#input)
    - [Textarea](#textarea)
    - [Select](#select)
    - [Async Searchable Select](#async-searchable-select)
    - [Checkbox](#checkbox)
    - [Radio](#radio)
    - [Toggle](#toggle)
    - [DatePicker](#datepicker)
  - [DataTable](#datatable)
    - [Basic DataTable Usage](#basic-datatable-usage)
    - [Column Builder](#column-builder)
    - [Row Actions](#row-actions)
    - [Sorting \& Searching](#sorting--searching)
  - [Charts](#charts)
    - [Line Chart](#line-chart)
    - [Bar Chart](#bar-chart)
    - [Pie \& Donut Chart](#pie--donut-chart)
    - [Area Chart](#area-chart)
    - [Live Polling](#live-polling)
    - [Custom Data Provider](#custom-data-provider)
  - [Caching](#caching)
    - [How it works](#how-it-works)
    - [DataTable caching](#datatable-caching)
    - [Chart caching](#chart-caching)
    - [Cache store configuration](#cache-store-configuration)
  - [Full Form Example](#full-form-example)
  - [Full Dashboard Example](#full-dashboard-example)
  - [UI Components](#ui-components)
    - [Alert](#alert)
    - [Avatar](#avatar)
    - [Breadcrumb](#breadcrumb)
    - [Loading](#loading)
    - [Progress](#progress)
    - [Rating](#rating)
    - [Stat](#stat)
    - [Steps](#steps)
    - [Timeline](#timeline)
    - [Error](#error)
    - [File Upload](#file-upload)
    - [Range Slider](#range-slider)
    - [Text Editor](#text-editor)
    - [Tags Input](#tags-input)
    - [Accordion](#accordion)
    - [Calendar](#calendar)
    - [Carousel](#carousel)
    - [Drawer](#drawer)
    - [Group](#group)
    - [Image Gallery](#image-gallery)
    - [Menu](#menu)
    - [Popover](#popover)
    - [Spotlight](#spotlight)
    - [Swap](#swap)
    - [Tabs](#tabs)
    - [Theme Toggle](#theme-toggle)
  - [Modal](#modal)
  - [Toast Notifications](#toast-notifications)
  - [Publishing Views](#publishing-views)
  - [Local Development (Workbench)](#local-development-workbench)
  - [Testing](#testing)
  - [Changelog](#changelog)
  - [Contributing](#contributing)
  - [Credits](#credits)
  - [License](#license)

---

## Installation

```bash
composer require centrex/tallui
```

Then run the install command to publish the config:

```bash
php artisan tallui:install
```

Publish views too (to customise templates):

```bash
php artisan tallui:install --views
```

Force-overwrite previously published files:

```bash
php artisan tallui:install --config --views --force
```

List all registered component tags for your current prefix:

```bash
php artisan tallui:list
```

The package works with **Tailwind CSS 3 (DaisyUI 4)** and **Tailwind CSS 4 (DaisyUI 5)**. Follow the setup for whichever version your project uses.

---

### Tailwind CSS 4 + DaisyUI 5

Tailwind CSS 4 uses a **CSS-first** configuration — no `tailwind.config.js` needed.

Install packages:

```bash
npm install tailwindcss @tailwindcss/vite daisyui
```

Configure Vite (`vite.config.js`):

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true }),
        tailwindcss(),
    ],
})
```

Configure your CSS (`resources/css/app.css`):

```css
@import "tailwindcss";
@plugin "daisyui";

/* Scan TallUI package views so their classes are not purged */
@source "../../vendor/centrex/tallui/resources/views";
```

---

### Tailwind CSS 3 + DaisyUI 4

Install packages:

```bash
npm install tailwindcss postcss autoprefixer daisyui
```

Configure Tailwind (`tailwind.config.js`) — add the package views to the `content` array so classes in TallUI's templates are not purged:

```js
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        // Include TallUI package views
        './vendor/centrex/tallui/resources/views/**/*.blade.php',
    ],
    plugins: [
        require('daisyui'),
    ],
    daisyui: {
        themes: ['light', 'dark'],
    },
}
```

Configure PostCSS (`postcss.config.js`):

```js
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
}
```

Configure your CSS (`resources/css/app.css`):

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

---

### Layout

Make sure `@livewireStyles` and `@livewireScripts` are present in your layout:

```html
<head>
    @livewireStyles
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
```

---

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="tallui-config"
```

```php
// config/tallui.php
return [
    // Component prefix: 'tallui' → <x-tallui-input />, <livewire:tallui-data-table />
    // Set to '' for shorter tags: <x-input />, <livewire:data-table />
    'prefix' => 'tallui',

    // Route prefix for internal package routes (e.g. async select search)
    'route_prefix' => '',

    'datatable' => [
        'per_page'         => 15,
        'per_page_options' => [10, 15, 25, 50, 100],
        'striped'          => true,
    ],

    'charts' => [
        'apexcharts_cdn' => 'https://cdn.jsdelivr.net/npm/apexcharts',
        'default_height' => 350,
        'default_poll'   => 0,      // 0 = no auto-refresh
        'theme'          => 'light',
    ],

    'forms' => [
        'size'              => 'md',  // 'xs' | 'sm' | 'md' | 'lg'
        'searchable_models' => [],    // register models for async select search
    ],
];
```

---

## Form Components

All form components:

- Are pure Blade components — no Livewire overhead per field
- Work with `wire:model`, `wire:model.live`, `x-model`, and plain HTML `name` attributes
- Accept arbitrary HTML attributes via attribute merging
- Use [DaisyUI 5](https://daisyui.com) component classes

### FormGroup

Wraps any input with a label and error/helper message — useful for consistent form layouts without repetition.

```blade
<x-tallui-form-group label="Full Name" for="name" :error="$errors->first('name')" required>
    <x-tallui-input name="name" wire:model="form.name" />
</x-tallui-form-group>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `label` | `?string` | `null` | Label text |
| `for` | `?string` | `null` | `<label for>` value |
| `helper` | `?string` | `null` | Helper text below the input |
| `error` | `?string` | `null` | Error message (replaces helper) |
| `required` | `bool` | `false` | Shows a red asterisk |

---

### Input

```blade
{{-- Basic --}}
<x-tallui-input name="email" label="Email" type="email" wire:model="email" />

{{-- With left icon and validation error --}}
<x-tallui-input
    name="email"
    label="Email address"
    type="email"
    placeholder="you@example.com"
    icon="o-envelope"
    :error="$errors->first('email')"
    required
    wire:model.live="email"
/>

{{-- Password with right icon --}}
<x-tallui-input name="password" label="Password" type="password" icon-right="o-lock-closed" />

{{-- Large size --}}
<x-tallui-input name="search" placeholder="Search..." size="lg" icon="o-magnifying-glass" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `''` | Input `name` and `id` |
| `type` | `string` | `'text'` | HTML input type |
| `label` | `?string` | `null` | Field label |
| `placeholder` | `?string` | `null` | Placeholder text |
| `helper` | `?string` | `null` | Helper text |
| `error` | `?string` | `null` | Error message |
| `icon` | `?string` | `null` | Heroicon name for left icon |
| `iconRight` | `?string` | `null` | Heroicon name for right icon |
| `required` | `bool` | `false` | Required attribute |
| `disabled` | `bool` | `false` | Disabled state |
| `size` | `string` | config `'md'` | `'xs'` \| `'sm'` \| `'md'` \| `'lg'` |

---

### Textarea

```blade
<x-tallui-textarea
    name="bio"
    label="Biography"
    placeholder="Tell us about yourself..."
    :rows="5"
    :error="$errors->first('bio')"
    wire:model="form.bio"
/>
```

Same props as `Input` plus `rows` (default `4`).

---

### Select

```blade
{{-- Static options --}}
<x-tallui-select
    name="role"
    label="Role"
    placeholder="Select a role..."
    :options="['admin' => 'Administrator', 'editor' => 'Editor', 'viewer' => 'Viewer']"
    :error="$errors->first('role')"
    wire:model="form.role"
/>

{{-- Options from a collection --}}
<x-tallui-select
    name="country_id"
    label="Country"
    :options="$countries->pluck('name', 'id')"
    wire:model="form.country_id"
/>

{{-- Inline <option> slots --}}
<x-tallui-select name="status" label="Status" wire:model="status">
    <option value="active">Active</option>
    <option value="inactive">Inactive</option>
</x-tallui-select>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `options` | `array` | `[]` | Associative `[value => label]` array |
| `placeholder` | `?string` | `null` | Disabled first option as placeholder |
| `searchable` | `bool` | `false` | Enable async search mode |
| `searchUrl` | `?string` | auto | Override the search endpoint URL |

---

### Async Searchable Select

For large datasets, enable `searchable` mode. The component renders an Alpine.js combobox that queries the package's allowlisted search endpoint.

**Step 1 — Register the model in config:**

```php
// config/tallui.php
'forms' => [
    'searchable_models' => [
        'country' => [
            'model'  => \App\Models\Country::class,
            'label'  => 'name',    // column shown in the dropdown
            'value'  => 'id',      // value stored in the hidden input
            'scope'  => 'active',  // optional Eloquent scope method
        ],
        'user' => [
            'model' => \App\Models\User::class,
            'label' => 'name',
            'value' => 'id',
        ],
    ],
],
```

**Step 2 — Use the component:**

```blade
<x-tallui-select
    name="country"
    label="Country"
    placeholder="Search countries..."
    :searchable="true"
    wire:model="form.country_id"
/>
```

> Only models listed in `searchable_models` can be queried. Any other name returns `403`.

---

### Checkbox

```blade
{{-- Basic --}}
<x-tallui-checkbox name="agree" label="I agree to the terms" wire:model="agree" />

{{-- With color and error --}}
<x-tallui-checkbox
    name="newsletter"
    label="Subscribe to newsletter"
    color="primary"
    :error="$errors->first('newsletter')"
    wire:model="newsletter"
/>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `color` | `string` | `''` | DaisyUI color: `primary`, `secondary`, `accent`, `success`, `warning`, `error` |
| `checked` | `bool` | `false` | Default checked state |

---

### Radio

```blade
<div class="flex flex-col gap-2">
    <x-tallui-radio name="plan" value="free"       label="Free"       wire:model="plan" />
    <x-tallui-radio name="plan" value="pro"        label="Pro"        wire:model="plan" />
    <x-tallui-radio name="plan" value="enterprise" label="Enterprise" wire:model="plan" />
</div>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `value` | `string` | `''` | Value submitted when this radio is selected |
| `color` | `string` | `''` | DaisyUI color |

---

### Toggle

```blade
<x-tallui-toggle name="notifications" label="Email notifications" color="success" wire:model="settings.notifications" />
<x-tallui-toggle name="dark_mode" label="Dark mode" color="primary" :checked="true" wire:model="settings.dark_mode" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `color` | `string` | `'primary'` | DaisyUI color |
| `checked` | `bool` | `false` | Default state |

---

### DatePicker

```blade
{{-- Date only --}}
<x-tallui-date-picker name="dob" label="Date of Birth" wire:model="form.dob" />

{{-- Date + time --}}
<x-tallui-date-picker
    name="scheduled_at"
    label="Schedule At"
    :with-time="true"
    min="{{ now()->toDateString() }}"
    wire:model="form.scheduled_at"
/>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `withTime` | `bool` | `false` | Renders `datetime-local` instead of `date` |
| `min` | `?string` | `null` | Minimum date (`YYYY-MM-DD`) |
| `max` | `?string` | `null` | Maximum date |

---

## DataTable

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

## Charts

Chart components are Livewire components powered by [ApexCharts](https://apexcharts.com). ApexCharts is loaded automatically from CDN via Livewire's `@assets` directive — no manual `<script>` tags needed.

All chart components share these props:

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `title` | `string` | `''` | Chart title |
| `subtitle` | `string` | `''` | Chart subtitle |
| `height` | `int` | config `350` | Chart height in px |
| `poll` | `int` | config `0` | Auto-refresh interval in ms (`0` = disabled) |
| `theme` | `string` | config `'light'` | `'light'` \| `'dark'` |
| `dataProvider` | `?string` | `null` | FQCN implementing `ChartDataProvider` |

---

### Line Chart

```blade
<livewire:tallui-line-chart
    title="Monthly Revenue"
    subtitle="Last 6 months"
    :height="300"
    :smooth="true"
    :dataProvider="\App\Charts\RevenueChart::class"
/>
```

| Extra Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `smooth` | `bool` | `false` | Smooth bezier curve vs straight lines |

**Inline data (for static / computed charts):**

```php
// app/Livewire/RevenueChart.php
namespace App\Livewire;

use Centrex\TallUi\Livewire\Charts\LineChart;

class RevenueChart extends LineChart
{
    protected function data(): array
    {
        return [
            'series'     => [
                ['name' => 'Revenue',  'data' => [4200, 5800, 4900, 7100, 6300, 8500]],
                ['name' => 'Expenses', 'data' => [2100, 2400, 2200, 3100, 2800, 3300]],
            ],
            'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        ];
    }
}
```

```blade
<livewire:revenue-chart title="Revenue vs Expenses" :poll="30000" />
```

---

### Bar Chart

```blade
<livewire:tallui-bar-chart
    title="Orders by Region"
    :horizontal="false"
    :dataProvider="\App\Charts\OrdersChart::class"
/>
```

| Extra Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `horizontal` | `bool` | `false` | Render as horizontal bar chart |

---

### Pie & Donut Chart

Pie/Donut charts use a flat `series` array (numeric values) and `categories` as labels:

```blade
<livewire:tallui-pie-chart
    title="Traffic Sources"
    :donut="true"
    :dataProvider="\App\Charts\TrafficChart::class"
/>
```

```php
// app/Charts/TrafficChart.php
namespace App\Charts;

use Centrex\TallUi\Contracts\ChartDataProvider;

class TrafficChart implements ChartDataProvider
{
    public function getData(): array
    {
        return [
            'series'     => [44, 28, 18, 10],
            'categories' => ['Organic', 'Referral', 'Social', 'Direct'],
        ];
    }
}
```

| Extra Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `donut` | `bool` | `false` | Render as donut instead of solid pie |

---

### Area Chart

```blade
<livewire:tallui-area-chart
    title="Active Users"
    :stacked="true"
    :poll="10000"
    :dataProvider="\App\Charts\ActiveUsersChart::class"
/>
```

| Extra Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `stacked` | `bool` | `false` | Stack series areas |

---

### Live Polling

Pass `poll` (milliseconds) to any chart to auto-refresh its data:

```blade
{{-- Refresh every 5 seconds --}}
<livewire:tallui-line-chart
    title="Live Server Load"
    :poll="5000"
    :dataProvider="\App\Charts\ServerLoadChart::class"
/>
```

The data provider is re-called on each poll cycle. The chart canvas is preserved between updates — only the series values change via `chart.updateOptions()`, keeping animations intact.

Set a global default for all charts:

```php
// config/tallui.php
'charts' => [
    'default_poll' => 10000,  // all charts refresh every 10 s unless overridden
],
```

---

### Custom Data Provider

Implement `ChartDataProvider` to feed a chart from any source — Eloquent, an external API, or a cache:

```php
// app/Charts/SalesChart.php
namespace App\Charts;

use App\Models\Order;
use Centrex\TallUi\Contracts\ChartDataProvider;
use Illuminate\Support\Facades\Cache;

class SalesChart implements ChartDataProvider
{
    public function getData(): array
    {
        return Cache::remember('chart.sales', 60, function (): array {
            $data = Order::query()
                ->selectRaw('MONTHNAME(created_at) as month, SUM(total) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderByRaw('MONTH(created_at)')
                ->get();

            return [
                'series'     => [['name' => 'Sales', 'data' => $data->pluck('total')->toArray()]],
                'categories' => $data->pluck('month')->toArray(),
            ];
        });
    }
}
```

```blade
<livewire:tallui-bar-chart
    title="Sales This Year"
    :poll="60000"
    :dataProvider="\App\Charts\SalesChart::class"
/>
```

---

## Caching

Both `DataTable` and chart components include built-in result caching via the `CachesData` trait. Caching is **disabled by default** (`$cacheTtl = 0`) and opt-in per component.

### How it works

| Component | What is cached | Cache key varies by |
| --- | --- | --- |
| DataTable | Paginated query results | class + search + sort + page + perPage + filters |
| Charts | Data provider / inline data output | class + dataProvider FQCN |

On **Redis or Memcached** (tag-supporting stores), invalidation uses cache tags — calling `invalidateCache()` flushes all keys for that component in one operation.

On **file or database** stores (no tag support), the trait maintains a key registry in cache and iterates to delete entries.

---

### DataTable caching

Set `$cacheTtl` (seconds) on your table class:

```php
class UsersTable extends DataTable
{
    public int $cacheTtl = 60;  // cache each page/search/sort combo for 60 seconds

    public function query(): Builder { ... }
    public function columns(): array { ... }
}
```

Invalidate when data changes (e.g. after a create/update/delete):

```php
#[On('userSaved')]
public function refresh(): void
{
    $this->invalidateCache();  // clears all cached pages for this table
    $this->dispatch('$refresh');
}
```

Use a specific cache store:

```php
public int    $cacheTtl   = 300;
public string $cacheStore = 'redis';
```

---

### Chart caching

Chart data is typically slower to generate than table queries (external APIs, heavy aggregations). Set `$cacheTtl` on the chart class or globally via config:

```php
// app/Livewire/RevenueChart.php
class RevenueChart extends LineChart
{
    public int $cacheTtl = 300;  // cache data for 5 minutes

    protected function data(): array { ... }
}
```

```blade
{{-- Or pass cacheTtl as a prop --}}
<livewire:tallui-bar-chart
    :dataProvider="\App\Charts\SalesChart::class"
    :cacheTtl="600"
    :poll="60000"
/>
```

Global default for all charts (applied in `mount()` if `$cacheTtl` is still 0):

```php
// config/tallui.php
'charts' => [
    'cache_ttl' => 300,  // 5 minutes for all charts
],
```

Invalidate from a listener or action:

```php
#[On('orderCreated')]
public function bust(): void
{
    $this->invalidateCache();
}
```

---

### Cache store configuration

```php
// config/tallui.php
'cache' => [
    'store' => 'redis',  // recommended: enables tag-based batch invalidation
],
```

Or per component:

```php
public ?string $cacheStore = 'redis';
```

> Tag-based invalidation (Redis/Memcached) is significantly faster than the file-store key-registry fallback for tables with many cached page combinations.

---

## Full Form Example

```php
// app/Livewire/UserForm.php
namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserForm extends Component
{
    public string $name      = '';
    public string $email     = '';
    public string $role      = '';
    public string $country   = '';
    public string $bio       = '';
    public string $dob       = '';
    public bool   $active    = true;
    public bool   $newsletter = false;

    protected array $rules = [
        'name'    => 'required|string|max:255',
        'email'   => 'required|email|unique:users,email',
        'role'    => 'required|in:admin,editor,viewer',
        'country' => 'required|exists:countries,id',
        'bio'     => 'nullable|string|max:1000',
        'dob'     => 'nullable|date',
    ];

    public function save(): void
    {
        $this->validate();

        User::create([
            'name'       => $this->name,
            'email'      => $this->email,
            'role'       => $this->role,
            'country_id' => $this->country,
            'bio'        => $this->bio,
            'dob'        => $this->dob,
            'active'     => $this->active,
        ]);

        session()->flash('success', 'User created successfully.');
    }

    public function render()
    {
        return view('livewire.user-form');
    }
}
```

```blade
{{-- resources/views/livewire/user-form.blade.php --}}
<form wire:submit="save" class="space-y-4 max-w-2xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-tallui-input
            name="name"
            label="Full Name"
            icon="o-user"
            :error="$errors->first('name')"
            wire:model="name"
            required
        />

        <x-tallui-input
            name="email"
            label="Email Address"
            type="email"
            icon="o-envelope"
            :error="$errors->first('email')"
            wire:model="email"
            required
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-tallui-select
            name="role"
            label="Role"
            placeholder="Choose role..."
            :options="['admin' => 'Administrator', 'editor' => 'Editor', 'viewer' => 'Viewer']"
            :error="$errors->first('role')"
            wire:model="role"
            required
        />

        <x-tallui-select
            name="country"
            label="Country"
            placeholder="Search countries..."
            :searchable="true"
            :error="$errors->first('country')"
            wire:model="country"
        />
    </div>

    <x-tallui-date-picker
        name="dob"
        label="Date of Birth"
        :error="$errors->first('dob')"
        wire:model="dob"
    />

    <x-tallui-textarea
        name="bio"
        label="Biography"
        placeholder="Tell us about this user..."
        :rows="4"
        :error="$errors->first('bio')"
        wire:model="bio"
    />

    <div class="flex flex-wrap gap-6">
        <x-tallui-toggle name="active" label="Active account" color="success" wire:model="active" />
        <x-tallui-checkbox name="newsletter" label="Subscribe to newsletter" wire:model="newsletter" />
    </div>

    <div class="flex gap-3 pt-2">
        <x-tallui-button type="submit" label="Create User" icon="o-check" spinner="save" />
        <x-tallui-button label="Cancel" link="{{ route('users.index') }}" />
    </div>
</form>
```

---

## Full Dashboard Example

```blade
<div class="space-y-6">

    {{-- KPI row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <livewire:tallui-line-chart title="Revenue" :poll="30000" :dataProvider="\App\Charts\RevenueChart::class" :height="200" />
        <livewire:tallui-bar-chart  title="Orders"  :poll="30000" :dataProvider="\App\Charts\OrdersChart::class"  :height="200" />
        <livewire:tallui-area-chart title="Users"   :poll="60000" :dataProvider="\App\Charts\UsersChart::class"   :height="200" />
        <livewire:tallui-pie-chart  title="Sources" :donut="true" :dataProvider="\App\Charts\TrafficChart::class" :height="200" />
    </div>

    {{-- Data table --}}
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h2 class="card-title">Recent Users</h2>
            <livewire:users-table />
        </div>
    </div>

</div>
```

---

## UI Components

---

### Alert

Dismissible alert banner with automatic icon selection per type.

```blade
<x-tallui-alert type="success" title="Saved!" dismissible>
    Your changes have been saved successfully.
</x-tallui-alert>

<x-tallui-alert type="error" title="Failed">
    Could not process your request. Please try again.
</x-tallui-alert>

<x-tallui-alert type="warning">
    Your subscription expires in 3 days.
</x-tallui-alert>

<x-tallui-alert type="info" icon="o-bell" :dismissible="false">
    New version available.
</x-tallui-alert>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `type` | `string` | `'info'` | `info` \| `success` \| `warning` \| `error` |
| `title` | `?string` | `null` | Bold heading line |
| `icon` | `?string` | auto | Override the icon (Heroicon name) |
| `dismissible` | `bool` | `false` | Show a close button |

---

### Avatar

User avatar with image, initials fallback, and presence indicator.

```blade
{{-- Image --}}
<x-tallui-avatar src="/storage/avatars/alice.jpg" alt="Alice" size="lg" :online="true" />

{{-- Initials with colour --}}
<x-tallui-avatar initials="BM" color="bg-primary text-primary-content" size="md" />

{{-- Placeholder icon --}}
<x-tallui-avatar size="sm" shape="square" />

{{-- With notification badge --}}
<x-tallui-avatar src="/img/user.jpg" badge="3" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `src` | `?string` | `null` | Image URL |
| `alt` | `string` | `''` | Image alt text |
| `initials` | `?string` | `null` | Up to 2 characters shown when no image |
| `size` | `string` | `'md'` | `xs` \| `sm` \| `md` \| `lg` |
| `shape` | `string` | `'circle'` | `circle` \| `square` \| `rounded` |
| `color` | `string` | `'bg-neutral text-neutral-content'` | Background + text colour when using initials |
| `online` | `bool` | `false` | Green online ring |
| `offline` | `bool` | `false` | Grey offline ring |
| `badge` | `?string` | `null` | Notification count badge |

---

### Breadcrumb

```blade
<x-tallui-breadcrumb :items="[
    ['label' => 'Home',     'url' => '/',          'icon' => 'o-home'],
    ['label' => 'Users',    'url' => '/users'],
    ['label' => 'Edit User'],
]" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `items` | `array` | `[]` | Each item: `label`, optional `url`, optional `icon` |

---

### Loading

```blade
{{-- Inline spinner --}}
<x-tallui-loading />

{{-- Sized and coloured --}}
<x-tallui-loading variant="dots" size="lg" color="text-primary" />

{{-- Inside a button --}}
<button class="btn btn-primary" wire:loading.attr="disabled">
    <x-tallui-loading variant="spinner" size="xs" wire:loading />
    Save
</button>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `variant` | `string` | `'spinner'` | `spinner` \| `dots` \| `ring` \| `ball` \| `bars` \| `infinity` |
| `size` | `string` | `'md'` | `xs` \| `sm` \| `md` \| `lg` |
| `color` | `?string` | `null` | Tailwind text colour, e.g. `text-primary` |

---

### Progress

```blade
<x-tallui-progress :value="65" color="primary" :show-label="true" label="Upload" />

<x-tallui-progress :value="3" :max="10" color="success" size="xs" />

<x-tallui-progress :value="0" color="warning" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `value` | `int\|float` | `0` | Current value |
| `max` | `int\|float` | `100` | Maximum value |
| `color` | `string` | `'primary'` | DaisyUI color |
| `size` | `string` | `'md'` | `xs` \| `sm` \| `md` \| `lg` |
| `showLabel` | `bool` | `false` | Show percentage on the right |
| `label` | `?string` | `null` | Text label on the left |

---

### Rating

```blade
{{-- Interactive --}}
<x-tallui-rating name="review_score" :value="3" :max="5" color="warning" />

{{-- Read-only display --}}
<x-tallui-rating name="avg" :value="4" :readonly="true" size="sm" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `'rating'` | Input name for form submission |
| `max` | `int` | `5` | Number of stars |
| `value` | `int\|float` | `0` | Current value |
| `size` | `string` | `'md'` | `xs` \| `sm` \| `md` \| `lg` |
| `color` | `string` | `'warning'` | DaisyUI color |
| `readonly` | `bool` | `false` | Disable interaction |

---

### Stat

```blade
<div class="stats shadow">
    <x-tallui-stat
        title="Total Users"
        value="14,290"
        desc="↗ 12% from last month"
        icon="o-users"
        icon-color="text-primary"
        change="+12%"
        change-type="up"
    />

    <x-tallui-stat title="Revenue" value="$84k" desc="Jan–Dec 2024" change="-3%" change-type="down" />
</div>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `title` | `string` | `''` | Stat label |
| `value` | `string` | `''` | Main stat number/text |
| `desc` | `?string` | `null` | Description line |
| `icon` | `?string` | `null` | Heroicon name shown as figure |
| `iconColor` | `string` | `'text-primary'` | Icon colour class |
| `change` | `?string` | `null` | Change text e.g. `+12%` |
| `changeType` | `string` | `'neutral'` | `up` (green) \| `down` (red) \| `neutral` |

---

### Steps

```blade
<x-tallui-steps
    :steps="[
        ['label' => 'Cart',     'color' => 'primary'],
        ['label' => 'Shipping', 'color' => 'primary'],
        ['label' => 'Payment'],
        ['label' => 'Confirm'],
    ]"
    :current="2"
/>

{{-- Vertical --}}
<x-tallui-steps :steps="[...]" :current="1" :vertical="true" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `steps` | `array` | `[]` | Each step: `label`, optional `color` |
| `current` | `int` | `1` | 1-based index of the active step (steps ≤ current are filled) |
| `vertical` | `bool` | `false` | Vertical layout |

---

### Timeline

```blade
<x-tallui-timeline :items="[
    [
        'time'        => 'Jan 2024',
        'title'       => 'Project Kickoff',
        'description' => 'Team assembled and roadmap finalized.',
        'color'       => 'primary',
        'icon'        => 'o-flag',
    ],
    [
        'time'        => 'Mar 2024',
        'title'       => 'Beta Launch',
        'description' => 'Public beta released to 500 testers.',
        'color'       => 'success',
    ],
    [
        'time'        => 'Jun 2024',
        'title'       => 'v1.0 Released',
        'color'       => 'accent',
    ],
]" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `items` | `array` | `[]` | Each item: `time`, `title`, optional `description`, `color`, `icon` |
| `compact` | `bool` | `false` | Compact layout (no start column) |

---

### Error

Renders the error message for a field from the `$errors` bag, or a custom message.

```blade
{{-- Auto-reads from $errors bag --}}
<x-tallui-error field="email" />

{{-- Custom message --}}
<x-tallui-error message="This value is not allowed." />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `field` | `string` | `''` | Field name to look up in `$errors` |
| `message` | `?string` | `null` | Override message (takes priority over field) |

---

### File Upload

```blade
<x-tallui-file
    name="avatar"
    label="Profile Photo"
    accept="image/*"
    helper="JPG, PNG or GIF up to 2MB"
    :error="$errors->first('avatar')"
/>

{{-- Multiple files --}}
<x-tallui-file
    name="documents"
    label="Attachments"
    :multiple="true"
    accept=".pdf,.docx"
    size="sm"
/>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `''` | Input name |
| `label` | `?string` | `null` | Field label |
| `accept` | `?string` | `null` | MIME type or extension filter |
| `multiple` | `bool` | `false` | Allow selecting multiple files |
| `bordered` | `bool` | `true` | Show border |
| `size` | `string` | config `'md'` | `xs` \| `sm` \| `md` \| `lg` |
| `error` | `?string` | `null` | Validation error |

---

### Range Slider

```blade
<x-tallui-range
    name="volume"
    label="Volume"
    :min="0"
    :max="100"
    :step="5"
    :value="40"
    color="primary"
    :show-value="true"
    :show-steps="true"
/>

{{-- Wired to Livewire --}}
<x-tallui-range name="budget" label="Budget" :min="0" :max="10000" :step="500"
    :value="$budget" :show-value="true" wire:model.live="budget" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `min` | `int` | `0` | Minimum value |
| `max` | `int` | `100` | Maximum value |
| `step` | `int` | `1` | Step increment |
| `value` | `?int` | `null` | Initial value |
| `color` | `string` | `'primary'` | DaisyUI color |
| `showValue` | `bool` | `false` | Display current value in the label |
| `showSteps` | `bool` | `false` | Show min/max tick marks below |

---

### Text Editor

A rich text editor using `contenteditable` — no external dependencies. Outputs raw HTML stored in a hidden input.

```blade
<x-tallui-text-editor
    name="body"
    label="Article Body"
    placeholder="Start writing..."
    :value="old('body', $article->body ?? '')"
    :error="$errors->first('body')"
    :rows="10"
/>
```

Toolbar provides: **Bold**, *Italic*, <u>Underline</u>, ~~Strikethrough~~, ordered/unordered lists, heading format selector (H2–H4, Paragraph, Quote), Undo/Redo.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `''` | Hidden input name for form submission |
| `value` | `?string` | `null` | Initial HTML content |
| `placeholder` | `?string` | `null` | Placeholder shown when empty |
| `rows` | `int` | `8` | Approximate height in lines |
| `error` | `?string` | `null` | Validation error |

---

### Tags Input

```blade
<x-tallui-tags
    name="skills"
    label="Skills"
    :value="old('skills', ['PHP', 'Laravel'])"
    placeholder="Add skill…"
    color="primary"
    :error="$errors->first('skills')"
/>
```

Press **Enter** or **,** to add a tag. Click **×** to remove.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `'tags'` | Submitted as `name[]` array |
| `value` | `array` | `[]` | Initial tags |
| `placeholder` | `?string` | `'Add tag…'` | Input placeholder |
| `color` | `string` | `'primary'` | Badge colour |

---

### Accordion

```blade
<x-tallui-accordion title="What is TallUI?" variant="arrow">
    TallUI is a Laravel UI component library built on Livewire, Tailwind CSS, and DaisyUI.
</x-tallui-accordion>

<x-tallui-accordion title="Pricing" variant="plus" :open="true" color="bg-base-200">
    All plans include unlimited components.
</x-tallui-accordion>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | `string` | `'accordion'` | Radio group name (use the same name to make them exclusive) |
| `title` | `string` | `''` | Panel header text |
| `open` | `bool` | `false` | Start expanded |
| `variant` | `string` | `'arrow'` | `arrow` \| `plus` |
| `color` | `string` | `''` | Background class e.g. `bg-base-200` |

---

### Calendar

```blade
{{-- Standalone --}}
<x-tallui-calendar
    :selected="today()->toDateString()"
    :events="[
        ['date' => '2024-06-10', 'label' => 'Team meeting',  'color' => 'primary'],
        ['date' => '2024-06-15', 'label' => 'Product launch', 'color' => 'success'],
    ]"
/>

{{-- Wired to Livewire --}}
<x-tallui-calendar wire="selectedDate" :selected="$selectedDate" />
```

Listens for `calendar-select` browser event: `{ date: 'YYYY-MM-DD' }`.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `selected` | `?string` | `null` | Pre-selected date (YYYY-MM-DD) |
| `month` | `?string` | current | Starting month (YYYY-MM) |
| `events` | `array` | `[]` | Each event: `date`, `label`, optional `color` |
| `selectable` | `bool` | `true` | Enable date selection |
| `wire` | `?string` | `null` | Livewire property to sync selected date into |

---

### Carousel

```blade
<x-tallui-carousel
    :items="[
        ['src' => '/img/slide1.jpg', 'alt' => 'Mountains', 'caption' => 'View from the top'],
        ['src' => '/img/slide2.jpg', 'alt' => 'Ocean'],
        ['src' => '/img/slide3.jpg', 'alt' => 'Forest'],
    ]"
    :arrows="true"
    :indicators="true"
    :autoplay="true"
    :interval="4000"
    height="h-80"
    fit="cover"
/>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `items` | `array` | `[]` | Each item: `src`, optional `alt`, `caption` |
| `arrows` | `bool` | `true` | Show prev/next arrows |
| `indicators` | `bool` | `true` | Show dot indicators |
| `autoplay` | `bool` | `false` | Auto-advance slides |
| `interval` | `int` | `3000` | Auto-advance delay in ms |
| `height` | `string` | `'h-64'` | Tailwind height class |
| `fit` | `string` | `'cover'` | `cover` \| `contain` |

---

### Drawer

```blade
<x-tallui-drawer id="settings-drawer" width="w-96">
    {{-- Main page content --}}
    <button onclick="document.getElementById('settings-drawer').checked = true" class="btn">
        Open Settings
    </button>

    {{-- Sidebar content --}}
    <x-slot:sidebar>
        <div class="p-6">
            <h2 class="text-lg font-bold mb-4">Settings</h2>
            {{-- settings form --}}
        </div>
    </x-slot:sidebar>
</x-tallui-drawer>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `id` | `string` | `'drawer'` | Checkbox input ID (use to open: `document.getElementById(id).checked = true`) |
| `side` | `string` | `'left'` | `left` \| `right` |
| `width` | `string` | `'w-80'` | Tailwind width class |
| `open` | `bool` | `false` | Start open |

---

### Group

Wraps children in a DaisyUI `join` so they share borders and appear as a single unit.

```blade
{{-- Button group --}}
<x-tallui-group>
    <button class="btn join-item">Left</button>
    <button class="btn btn-active join-item">Center</button>
    <button class="btn join-item">Right</button>
</x-tallui-group>

{{-- Vertical input + button --}}
<x-tallui-group :vertical="true">
    <input class="input input-bordered join-item" placeholder="Email" />
    <button class="btn btn-primary join-item">Subscribe</button>
</x-tallui-group>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `vertical` | `bool` | `false` | Stack children vertically |

---

### Image Gallery

```blade
<x-tallui-image-gallery
    :images="[
        ['src' => '/img/a.jpg', 'alt' => 'Sunset',   'caption' => 'Golden hour'],
        ['src' => '/img/b.jpg', 'alt' => 'Mountains'],
        ['src' => '/img/c.jpg', 'alt' => 'City',     'caption' => 'Downtown at night'],
    ]"
    :columns="3"
    :lightbox="true"
    height="h-48"
    fit="cover"
/>
```

Arrow keys navigate in the lightbox; Escape closes it.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `images` | `array` | `[]` | Each image: `src`, optional `alt`, `caption` |
| `columns` | `int` | `3` | Grid columns |
| `lightbox` | `bool` | `true` | Enable click-to-zoom lightbox |
| `height` | `string` | `'h-48'` | Grid thumbnail height |
| `fit` | `string` | `'cover'` | `cover` \| `contain` |

---

### Menu

```blade
<x-tallui-menu :items="[
    ['label' => 'Dashboard', 'url' => '/',        'icon' => 'o-home'],
    ['label' => 'Users',     'url' => '/users',   'icon' => 'o-users', 'active' => true, 'badge' => '12'],
    ['label' => 'Settings',  'icon' => 'o-cog-6-tooth', 'children' => [
        ['label' => 'Profile',   'url' => '/settings/profile'],
        ['label' => 'Security',  'url' => '/settings/security'],
    ]],
]" />
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `items` | `array` | `[]` | Each item: `label`, `url`?, `icon`?, `active`?, `badge`?, `children`? |
| `horizontal` | `bool` | `false` | Horizontal layout |
| `size` | `string` | `''` | `xs` \| `sm` \| `lg` |
| `color` | `string` | `''` | Background class |

---

### Popover

```blade
<x-tallui-popover position="top" trigger="hover">
    <x-slot:trigger_slot>
        <button class="btn btn-sm">Hover me</button>
    </x-slot:trigger_slot>
    <x-slot:content>
        <p class="font-semibold">Quick tip</p>
        <p class="text-base-content/70 text-xs mt-1">This appears on hover.</p>
    </x-slot:content>
</x-tallui-popover>

{{-- Click trigger --}}
<x-tallui-popover position="bottom" trigger="click">
    <x-slot:trigger_slot><button class="btn">Click</button></x-slot:trigger_slot>
    <x-slot:content>Popover content here.</x-slot:content>
</x-tallui-popover>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `position` | `string` | `'top'` | `top` \| `bottom` \| `left` \| `right` |
| `trigger` | `string` | `'hover'` | `hover` \| `click` |

---

### Spotlight

A ⌘K command-palette overlay with fuzzy search.

```blade
{{-- Trigger button (place in navbar) --}}
<x-tallui-spotlight
    placeholder="Search pages, users, actions..."
    :items="[
        ['label' => 'Dashboard',     'url' => '/',        'group' => 'Pages',   'description' => 'Overview & charts'],
        ['label' => 'Users',         'url' => '/users',   'group' => 'Pages'],
        ['label' => 'New User',      'url' => '/users/create', 'group' => 'Actions'],
        ['label' => 'Documentation', 'url' => '/docs',    'group' => 'Links'],
    ]"
>
    Search
</x-tallui-spotlight>
```

Opens with **⌘K** / **Ctrl+K** or the `/` key (when not in an input). Arrow keys navigate, Enter follows the link.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `placeholder` | `string` | `'Search anything...'` | Input placeholder |
| `shortcut` | `string` | `'/'` | Single-key shortcut to open (set `''` to disable) |
| `items` | `array` | `[]` | Each item: `label`, `url`?, `description`?, `group`?, `icon`? |

---

### Swap

Toggle between two states with a rotate or flip animation (DaisyUI `swap`).

```blade
{{-- Sound toggle --}}
<x-tallui-swap effect="rotate">
    <x-slot:on>🔊</x-slot:on>
    <x-slot:off>🔇</x-slot:off>
</x-tallui-swap>

{{-- Hamburger / close --}}
<x-tallui-swap effect="rotate">
    <x-slot:on>
        <svg class="w-6 h-6" ...><!-- X icon --></svg>
    </x-slot:on>
    <x-slot:off>
        <svg class="w-6 h-6" ...><!-- Hamburger icon --></svg>
    </x-slot:off>
</x-tallui-swap>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `active` | `bool` | `false` | Start in the "on" state |
| `effect` | `string` | `'rotate'` | `rotate` \| `flip` |

---

### Tabs

```blade
<x-tallui-tab
    :tabs="[
        ['id' => 'overview',  'label' => 'Overview',  'icon' => 'o-home'],
        ['id' => 'activity',  'label' => 'Activity'],
        ['id' => 'settings',  'label' => 'Settings',  'icon' => 'o-cog-6-tooth'],
    ]"
    active="overview"
    variant="bordered"
>
    <x-slot:overview>
        <p>Overview panel content.</p>
    </x-slot:overview>

    <x-slot:activity>
        <p>Activity feed goes here.</p>
    </x-slot:activity>

    <x-slot:settings>
        <p>Settings form goes here.</p>
    </x-slot:settings>
</x-tallui-tab>
```

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `tabs` | `array` | `[]` | Each tab: `id`, `label`, optional `icon` |
| `active` | `string` | first tab id | Initially active tab id |
| `variant` | `string` | `'bordered'` | `bordered` \| `lifted` \| `boxed` |
| `size` | `string` | `''` | `xs` \| `sm` \| `lg` |

---

### Theme Toggle

```blade
{{-- Icon-only toggle --}}
<x-tallui-theme-toggle />

{{-- With label --}}
<x-tallui-theme-toggle :with-label="true" light="light" dark="dark" />
```

Persists the selected theme to `localStorage` and sets the `data-theme` attribute on `<html>`.

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `light` | `string` | `'light'` | DaisyUI light theme name |
| `dark` | `string` | `'dark'` | DaisyUI dark theme name |
| `withLabel` | `bool` | `false` | Show current theme name next to the icon |

---

## Modal

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

## Toast Notifications

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

## Publishing Views

To customise component templates:

```bash
php artisan vendor:publish --tag="tallui-views"
```

Views are published to `resources/views/vendor/tallui/`. Edited files take precedence over the package defaults.

---

## Local Development (Workbench)

The package ships with a workbench demo app powered by [Orchestra Testbench](https://packages.tools/testbench).

```bash
# Discover package, run migrations, seed demo data, build assets
composer build

# Start the local dev server at http://localhost:8000
composer start
```

`composer start` is equivalent to:

```bash
vendor/bin/testbench workbench:build --ansi
vendor/bin/testbench serve
```

To seed the database independently:

```bash
vendor/bin/testbench migrate:fresh --seed
```

---

## Testing

```bash
# Fix code style with Pint
composer lint

# Check code style without fixing
composer test:lint

# Apply Rector refactors
composer refacto

# Dry-run Rector (check only)
composer test:refacto

# Static analysis with PHPStan/Larastan
composer test:types

# Run unit & feature tests with Pest (parallel)
composer test:unit

# Full suite: refacto check + lint check + types + tests
composer test

# Tests with code coverage report
composer test-coverage
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [rochi88](https://github.com/centrex)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
