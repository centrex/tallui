# Caching

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

← [Back to docs](../README.md)
