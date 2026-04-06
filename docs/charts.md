# Charts

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

← [Back to docs](../README.md)
