# TallUI — Laravel UI Components Library

[![Latest Version on Packagist](https://img.shields.io/packagist/v/centrex/tallui.svg?style=flat-square)](https://packagist.org/packages/centrex/tallui)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/centrex/tallui/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/centrex/tallui/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/centrex/tallui/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/centrex/tallui/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/centrex/tallui?style=flat-square)](https://packagist.org/packages/centrex/tallui)

A full-featured UI component library for Laravel + Livewire + Tailwind CSS + DaisyUI. Ships server-driven **data tables**, reactive **form components**, live **chart components**, and a rich set of **UI primitives** — all powered by Alpine.js with no extra JS build step.

---

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

## Quick Start

```bash
composer require centrex/tallui
php artisan tallui:install
```

See [Installation →](docs/installation.md) for Tailwind 3/4 and DaisyUI 4/5 setup.

---

## Documentation

| Guide | What's inside |
| --- | --- |
| [Installation](docs/installation.md) | Composer, Tailwind CSS 3 & 4, DaisyUI 4 & 5, layout setup |
| [Configuration](docs/configuration.md) | `config/tallui.php` — prefix, datatable, charts, forms, cache |
| [Form Components](docs/forms.md) | Input, Textarea, Select, Checkbox, Radio, Toggle, DatePicker, Range, File, TextEditor, Tags |
| [DataTable](docs/datatable.md) | Column builder, row actions, filters, sorting, searching, async search |
| [Charts](docs/charts.md) | Line, Bar, Pie/Donut, Area — live polling, data providers, caching |
| [Caching](docs/caching.md) | DataTable & chart result caching, tag-based invalidation |
| [UI Components](docs/ui-components.md) | Alert, Avatar, Breadcrumb, Loading, Progress, Rating, Stat, Steps, Timeline, Error |
| [Interactive Components](docs/ui-components.md#accordion) | Accordion, Calendar, Carousel, Drawer, Group, Image Gallery, Menu, Popover, Spotlight, Swap, Tabs, Theme Toggle |
| [Modal](docs/modal.md) | Dialog component — open/close events, sizes, slots, Livewire integration |
| [Toast Notifications](docs/toast.md) | `Toast` trait — success/error/warning/info with auto-dismiss |
| [Examples](docs/examples.md) | Full form example, full dashboard example |
| [Development & Testing](docs/development.md) | Workbench, composer scripts, Pest, PHPStan, Rector |

---

## Artisan Commands

```bash
# Publish config (and optionally views)
php artisan tallui:install
php artisan tallui:install --views
php artisan tallui:install --config --views --force

# List all registered component tags
php artisan tallui:list
```

---

## Component Tags (default prefix `tallui`)

### Form

| Tag | Description |
| --- | --- |
| `<x-tallui-input />` | Text / email / number / password field |
| `<x-tallui-textarea />` | Multi-line textarea |
| `<x-tallui-select />` | Static or async-searchable select |
| `<x-tallui-checkbox />` | Checkbox with label |
| `<x-tallui-radio />` | Radio button |
| `<x-tallui-toggle />` | Toggle switch |
| `<x-tallui-range />` | Range slider |
| `<x-tallui-file />` | File / multi-file upload |
| `<x-tallui-date-picker />` | Date / datetime-local picker |
| `<x-tallui-text-editor />` | Rich text editor (contenteditable) |
| `<x-tallui-tags />` | Tag input with add/remove |
| `<x-tallui-form-group />` | Label + helper/error wrapper |
| `<x-tallui-error />` | Inline validation error |

### Display

| Tag | Description |
| --- | --- |
| `<x-tallui-alert />` | Dismissible info/success/warning/error banner |
| `<x-tallui-avatar />` | Image, initials, or placeholder avatar |
| `<x-tallui-badge />` | Status badge |
| `<x-tallui-breadcrumb />` | Breadcrumb trail |
| `<x-tallui-loading />` | Spinner / dots / ring / bars indicator |
| `<x-tallui-progress />` | Progress bar |
| `<x-tallui-rating />` | Star rating input or display |
| `<x-tallui-stat />` | KPI stat block |
| `<x-tallui-steps />` | Multi-step progress indicator |
| `<x-tallui-timeline />` | Vertical event timeline |

### Interactive / Layout

| Tag | Description |
| --- | --- |
| `<x-tallui-accordion />` | Collapsible panel |
| `<x-tallui-button />` | Button with icon and loading state |
| `<x-tallui-calendar />` | Month calendar with events |
| `<x-tallui-carousel />` | Image slider with autoplay |
| `<x-tallui-drawer />` | Slide-in sidebar |
| `<x-tallui-group />` | DaisyUI join wrapper |
| `<x-tallui-image-gallery />` | Image grid with lightbox |
| `<x-tallui-menu />` | Menu with nested children |
| `<x-tallui-modal />` | Dialog with open/close events |
| `<x-tallui-popover />` | Hover/click tooltip |
| `<x-tallui-spotlight />` | ⌘K command palette |
| `<x-tallui-swap />` | Toggle between two states |
| `<x-tallui-tab />` | Tabbed panels |
| `<x-tallui-theme-toggle />` | Light/dark theme switcher |
| `<x-tallui-toast />` | Toast notification container |

### Livewire

| Tag | Description |
| --- | --- |
| `<livewire:tallui-data-table />` | Server-driven sortable/filterable table |
| `<livewire:tallui-line-chart />` | ApexCharts line chart |
| `<livewire:tallui-bar-chart />` | ApexCharts bar chart |
| `<livewire:tallui-pie-chart />` | ApexCharts pie / donut chart |
| `<livewire:tallui-area-chart />` | ApexCharts area chart |

> The prefix is configurable. Set `'prefix' => ''` in `config/tallui.php` for shorter tags like `<x-input />`.

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
