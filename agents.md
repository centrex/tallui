# agents.md

## Agent Guidance — tallui

### Package Purpose
Reusable Blade and Livewire UI components for the TALL stack (Tailwind, Alpine.js, Livewire, Laravel), built on DaisyUI. Provides page layout, data display, form elements, charts, and a powerful DataTable.

### Before Making Changes
- Read `src/TallUiServiceProvider.php` — component registration, prefix config
- Read `resources/views/components/` — Blade templates for each component
- Read `src/Livewire/DataTable.php` — base class for all DataTable implementations
- Read `src/DataTable/Column.php` — column definition API
- Read `config/tallui.php` — component prefix and other options

### Common Tasks

**Adding a new Blade component**
1. Create the Blade template in `resources/views/components/<name>.blade.php`
2. Create the component class in `src/View/` (or use anonymous component if no PHP logic needed)
3. Register in `TallUiServiceProvider` or rely on auto-discovery
4. Add docs to root `CLAUDE.md` usage section
5. Add tests

**Adding a new Livewire component**
1. Create the class in `src/Livewire/<Name>.php`
2. Create the view in `resources/views/livewire/<name>.blade.php`
3. Register the alias in `TallUiServiceProvider` (e.g., `tallui-name`)
4. Expose props consistently with existing components

**Adding a DataTable column feature**
1. Add the method to `src/DataTable/Column.php` (fluent interface — return `$this`)
2. Handle the new feature in `src/Livewire/DataTable.php` (query building, view rendering)
3. Update the Blade template in `resources/views/livewire/` to render the new feature
4. Add tests

**Adding a new chart type**
1. Create `src/Livewire/<Type>Chart.php` extending a base chart class
2. Create the view in `resources/views/livewire/<type>-chart.blade.php`
3. Charts use ApexCharts via Alpine — the `$series` and `$categories` props are the primary data interface
4. Register in service provider

### Component Design Rules
- All components pass through `$attributes` — use `$attributes->merge([])` not manual class concatenation
- DaisyUI classes are the base — never use raw Tailwind colors for semantic states (use `btn-primary`, `text-error`, etc.)
- Props should have sensible defaults — no required props unless truly mandatory
- Emit Livewire events rather than coupling components directly

### Testing
```sh
composer test:unit        # pest
composer test:types       # phpstan
composer test:lint        # pint
```

Test component rendering:
```php
$this->blade('<x-tallui-badge type="success">Active</x-tallui-badge>')
     ->assertSee('Active')
     ->assertSee('badge-success');
```

Test DataTable via Livewire:
```php
Livewire::test(MyTable::class)
    ->assertSee('Expected Row')
    ->set('search', 'query')
    ->assertSee('Filtered Row');
```

### Safe Operations
- Adding new Blade components
- Adding new Livewire component props (with defaults)
- Adding DataTable column fluent methods
- Adding tests

### Risky Operations — Confirm Before Doing
- Changing the component prefix default (`tallui`) — breaks all host app `<x-tallui-*>` usage
- Renaming existing component files — breaks host apps using those components
- Changing `DataTable` public method signatures (`columns()`, `query()`) — breaks all subclasses
- Changing `Column::make()` constructor signature

### Do Not
- Use raw `<div class="text-red-500">` for semantic states — use DaisyUI semantic classes
- Add JavaScript directly to PHP classes — keep JS in Alpine `x-data` in Blade templates
- Remove `$attributes->merge()` from components — it breaks host app class customization
- Skip `declare(strict_types=1)` in any new file
