# Configuration

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

← [Back to docs](../README.md)
