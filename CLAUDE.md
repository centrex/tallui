# CLAUDE.md

## Package Overview

`centrex/tallui` — Reusable Blade and Livewire UI components built on DaisyUI + Alpine.js for the TALL stack.

Namespace: `Centrex\TallUi\`  
Service Provider: `TallUiServiceProvider`  
Component prefix: `tallui` (configurable)

> Full component usage documentation (page-header, card, stats, badges, alerts, empty-state, buttons, notifications, DataTable, charts) is in the **root `CLAUDE.md`** of the `laravel_plugins` monorepo.

## Commands

Run from inside this directory (`cd tallui`):

```sh
composer install          # install dependencies
composer test             # full suite: rector dry-run, pint check, phpstan, pest
composer test:unit        # pest tests only
composer test:lint        # pint style check (read-only)
composer test:types       # phpstan static analysis
composer test:refacto     # rector refactor check (read-only)
composer lint             # apply pint formatting
composer refacto          # apply rector refactors
composer analyse          # phpstan (alias)
composer build            # prepare testbench workbench
composer start            # build + serve testbench dev server
```

Run a single test:
```sh
vendor/bin/pest tests/ExampleTest.php
vendor/bin/pest --filter "test name"
```

## Structure

```
src/
  TallUi.php
  TallUiServiceProvider.php
  Facades/
  Commands/
  Concerns/
  Contracts/
  DataTable/
    Column.php                  # Column definition for DataTable
  Http/
  Livewire/
    DataTable.php               # Base DataTable Livewire component
    LineChart.php / BarChart.php / PieChart.php / AreaChart.php
  Traits/
  View/
resources/
  views/
    components/                 # Blade component templates
    livewire/                   # Livewire component templates
config/tallui.php
tests/
workbench/
```

## Component Registration

All Blade components are auto-discovered from `resources/views/components/` and registered with the prefix from `config/tallui.php` (`prefix` key, default `tallui`). Components render as `<x-tallui-*>`.

Livewire components are registered in `TallUiServiceProvider` and aliased as `tallui-*`.

## DataTable Extension Pattern

```php
use Centrex\TallUi\DataTable\Column;
use Centrex\TallUi\Livewire\DataTable;
use Illuminate\Database\Eloquent\Builder;

class MyTable extends DataTable
{
    public function columns(): array { ... }
    public function query(): Builder { ... }
}
```

Features: URL-synced search/sort/page, per-page selector, row selection, CSV export (chunked, UTF-8 BOM), optional result caching via `$cacheTtl`.

## New Components (added 2026-04-08)

| Component tag | Class | Notes |
|---|---|---|
| `<x-tallui-sidebar>` | `View\Components\Sidebar` | Slide-in panel (left/right), overlay, persistent on lg+ |
| `<x-tallui-dialog>` | `View\Components\Dialog` | Centred confirmation/info dialog, auto-icon per type |
| `<x-tallui-collapse>` | `View\Components\Collapse` | Single collapsible panel with Alpine transitions |
| `<x-tallui-header>` | `View\Components\Header` | Sticky app navbar with brand, center, actions slots |
| `<x-tallui-image-library>` | `View\Components\ImageLibrary` | Selectable image grid with lightbox |
| `<x-tallui-choices>` | `View\Components\Form\Choices` | Multi-select with search and tag badges |
| `<x-tallui-file-upload>` | `View\Components\Form\FileUpload` | Drag-and-drop zone with image previews |
| `<x-tallui-pin>` | `View\Components\Form\Pin` | PIN / OTP input (auto-advance, paste, backspace) |
| `<x-tallui-password-input>` | `View\Components\Form\PasswordInput` | Password field with show/hide toggle (Alpine.js) |

## Performance Blade Directives

All registered in `TallUiServiceProvider::registerBladeDirectives()`.

### `@pushonce` / `@endpushonce`
Push to a named stack only once per request — prevents duplicate `<script>`/`<link>` tags when a component is used multiple times.
```blade
@pushonce('scripts', 'my-lib-js')
    <script src="/vendor/my-lib.js"></script>
@endpushonce
```

### `@memoize` / `@endmemoize`
Render a Blade block once per request and replay the cached HTML on subsequent calls. Use inside loops for expensive sub-views.
```blade
@foreach($rows as $row)
    @memoize('nav-icons')
        {{-- expensive icon rendering only happens once --}}
        <x-tallui-icon name="heroicon-o-check" />
    @endmemoize
@endforeach
```

### `@lazy` / `@endlazy`
Defers Alpine initialisation of the wrapped content until it scrolls near the viewport (via IntersectionObserver / Alpine `x-intersect`).
```blade
@lazy
    <livewire:tallui-bar-chart :series="$series" />
@endlazy
```

### `@styleonce` / `@endstyleonce`
Push a `<style>` block to the `styles` stack at most once.
```blade
@styleonce('tallui-pin')
    <style>.pin-input { letter-spacing: 0.5em; }</style>
@endstyleonce
```

### `@scriptonce` / `@endscriptonce`
Push a `<script>` block to the `scripts` stack at most once.
```blade
@scriptonce('apexcharts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endscriptonce
```

## Conventions

- PHP 8.2+, `declare(strict_types=1)` in all files
- Pest for tests, snake_case test names
- Pint with `laravel` preset
- Rector targeting PHP 8.3 with `CODE_QUALITY`, `DEAD_CODE`, `EARLY_RETURN`, `TYPE_DECLARATION`, `PRIVATIZATION` sets
- PHPStan at level `max` with Larastan
