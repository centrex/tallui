# Known Issues — tallui

_Last checked: 2026-08-02_

## Failing tests

`vendor/bin/pest -p` reports **49 failed, 242 passed (448 assertions)**. They fall into a handful of distinct root causes rather than 49 unrelated bugs:

- **Chart Livewire components missing methods used by their own tests** (`Tests\Feature\Charts\ChartTest`, ~8 failures) — `BadMethodCallException`:
  - `Centrex\TallUi\Livewire\Charts\LineChart::defaultOptions does not exist.` (`tests/Feature/Charts/ChartTest.php:43`)
  - `Centrex\TallUi\Livewire\Charts\BarChart::defaultOptions does not exist.` (`:70`)
  - `Centrex\TallUi\Livewire\Charts\PieChart::chartType does not exist.` (`:84`)
  - `Centrex\TallUi\Livewire\Charts\AreaChart::defaultOptions does not exist.` (`:124`)
  - Plus the related "it includes ApexCharts CDN script" assertion failure — the CDN `<script src="https://cdn.jsdelivr.net/npm/apexcharts">` is not present in the rendered chart HTML (`tests/Feature/Charts/ChartTest.php:48`). Either the chart classes lost these methods in a refactor, or the tests target an API surface (`defaultOptions()`/`chartType()`) the components never actually expose.

- **DataTable feature tests reference an undefined route** (`DataTableRenderTest`, `DataTableSearchTest`, `DataTableSortTest`, `DataTablePaginationTest`, `DataTableCustomHtmlTest`, ~24 failures) — `ViewException: Route [users.edit] not defined.` from `resources/views/livewire/data-table.blade.php`. The test fixture table (`tests/Fixtures/DataTables/UsersTable.php` or similar) builds an action link via `route('users.edit', ...)`, but the workbench/testbench app used by these tests never registers a `users.edit` route. This looks like a test-harness setup gap rather than a product bug.

- **`Tests\Unit\Concerns\CachesDataTest`** (4 failures) — `Error: Call to protected method CacheHost::cacheKey()/rememberCache()/rememberCacheTracked() from scope P\Tests\Unit\Concerns\CachesDataTest`. The test fixture (`CacheHost`) calls these `CachesData` trait methods directly from the test, but the trait declares them `protected` — either the trait's visibility needs to be `public` (if meant to be called externally) or the test needs a public wrapper method on `CacheHost`.

- **`Tests\Feature\DataTable\DataTableFilterTest`** (2 failures) — one `PublicPropertyNotFoundException: Public property [$page] not found on component` (`tests/Feature/DataTable/DataTableFilterTest.php:148`), and one array-equality assertion expecting `['status' => null]` but getting `['is_active' => '1']` — looks like the test was written against an older filter key name (`status`) that the DataTable/fixture has since renamed to `is_active`.

- **`Tests\Feature\DataTable\DataTableCustomHtmlTest` — "html() renderer ... escapes untrusted content"** — `assertDontSee('<script>xss</script>')` (escaped) fails because the escaped string `&lt;script&gt;xss&lt;/script&gt;` **is** present in the output. Read literally this looks alarming, but given `assertDontSee()` HTML-escapes its needle by default, this actually indicates the renderer correctly escaped the payload — the test assertion itself appears inverted (should be `assertSee`, not `assertDontSee`) rather than a real XSS hole. Worth a second look before treating it as a security bug.

- **`Tests\Feature\Form\OtherFormComponentsTest`** (4 failures, `Checkbox`/`Radio`/`Toggle`) — e.g. `expect($c->render())->toContain('plan-pro')` / `toContain('radio-success')` / `toContain('toggle-success')` fail against the rendered template. The rendered output shown in the diff starts with `<div @class([...])>` — i.e. the **unevaluated Blade directive source** rather than compiled HTML, suggesting `render()` on these components isn't going through the Blade compiler in the test context (or the fixture doesn't set up a view factory), not a problem with the components' color/id logic itself.

## Style / static-analysis debt

- `vendor/bin/pint --test` reports **9 files** with unapplied fixers, mostly `new_with_braces` (`new Foo()` → `new Foo`) and one `binary_operator_spaces`: `src/Http/Controllers/SelectSearchController.php`, `src/TallUiServiceProvider.php`, `src/View/Components/MenuItem.php`, `tests/Unit/Concerns/CachesDataTest.php`, `tests/Unit/Concerns/HasUuidTest.php`, `tests/Feature/NewComponentsTest.php`, `tests/Feature/DataTable/DataTableCustomHtmlTest.php`, `tests/Feature/DataTableViewRenderTest.php`, `tests/Feature/Form/OtherFormComponentsTest.php`.
- `vendor/bin/rector --dry-run` reports **84 files** would change, essentially all the single rule `AddOverrideAttributeToOverriddenMethodsRector` (missing `#[\Override]` on overridden methods) across `src/` and `tests/`.
- `vendor/bin/phpstan analyse` **cannot run** — fails immediately with:
  ```
  Invalid configuration:
  Unexpected item 'parameters › checkOctaneCompatibility'.
  Invalid configuration:
  Unexpected item 'parameters › checkModelProperties'.
  ```
  `phpstan.neon.dist` sets `checkOctaneCompatibility` and `checkModelProperties`, which are Larastan 3.x config options, but `composer.json` constrains `larastan/larastan` to `^2.0` (installed: `2.11.2`), which doesn't recognize those keys. Either bump the `larastan/larastan` constraint to `^3.0` or remove those two config keys from `phpstan.neon.dist` — static analysis is currently not running at all, not just "failing with errors."

## TODO / FIXME markers

None found (`grep -rn "TODO\|FIXME" --include="*.php" src/ config/ tests/`).

## Open GitHub issues

Not checked — the `gh` CLI is not installed in this environment.

## Notes — `Column::currency()` API-mismatch report from laravel-accounting

`laravel-accounting`'s own `KNOWN_ISSUES.md` reports `BillsLivewirePaymentTest` failing with `Call to undefined method Centrex\TallUi\DataTable\Column::currency()`, thrown from `src/Livewire/BillTable.php:38` (`Column::make('Total', 'base_total')->currency($currency)`).

Checked directly against **this** package's actual source (`src/DataTable/Column.php`): `Column` **does** expose a `currency()` method —

```php
/**
 * Shorthand for ->format('currency:CODE').
 */
public function currency(string $currencyCode = 'USD'): static
{
    $this->format = 'currency:' . $currencyCode;

    return $this;
}
```

(line 153, added in commit `a5d050a`, 2026-07-11), in addition to the more general `->format('currency:EUR')` hint accepted by `->format()`. So the two APIs `laravel-accounting` might expect (`->currency($code)` and `->format('currency:CODE')`) both exist here today.

This means the failure observed in `laravel-accounting` is almost certainly **not** because tallui's API lacks `currency()` — it's because the `centrex/tallui` version actually resolved into `laravel-accounting`'s own `vendor/` (via its `composer.json` constraint, likely a tagged release predating 2026-07-11) is older than this checked-out source and doesn't have the method yet. Confirm which tagged `centrex/tallui` release `laravel-accounting` is actually pinned/locked to before treating either side as "the bug" — the fix is most likely to bump/re-tag `centrex/tallui` and update `laravel-accounting`'s constraint/lock, not to change either `Column` API.
