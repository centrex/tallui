<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /**
     * Default component prefix.
     *
     * Make sure to clear view cache after renaming with `php artisan view:clear`
     *
     *    prefix => ''
     *              <x-button />
     *              <livewire:data-table />
     *
     *    prefix => 'tallui'
     *               <x-tallui-button />
     *               <livewire:tallui-data-table />
     */
    'prefix' => 'tallui',

    /**
     * Default route prefix.
     *
     * Some TallUI components make network requests to internal routes.
     *
     *      route_prefix => ''
     *          - Select search: '/tallui/select-search'
     *
     *      route_prefix => 'my-components'
     *          - Select search: '/my-components/tallui/select-search'
     */
    'route_prefix' => '',

    /**
     * DataTable component settings.
     */
    'datatable' => [
        'per_page'         => 15,
        'per_page_options' => [10, 15, 25, 50, 100],
        'striped'          => true,
    ],

    /**
     * Chart component settings.
     */
    'charts' => [
        'apexcharts_cdn' => 'https://cdn.jsdelivr.net/npm/apexcharts',
        'default_height' => 350,
        'default_poll'   => 0,
        'theme'          => 'light',
        'cache_ttl'      => 0,   // seconds; 0 = disabled (override per component via $cacheTtl)
    ],

    'cache' => [
        'store' => null,   // null = default store; set to 'redis' for tag-based invalidation
    ],

    /**
     * Form component settings.
     */
    'forms' => [
        'size'              => 'md',
        'searchable_models' => [],
    ],

    /**
     * Components settings
     */
    'components' => [
        'spotlight' => [
            'class' => 'App\Support\Spotlight',
        ],
    ],

    /**
     * Searchable models for the Select component.
     */
    'forms' => [
        'searchable_models' => [
            'user' => [
                'model' => App\Models\User::class,
                'label' => 'name',
                'value' => 'id',
                'search_columns' => ['name', 'email'],
                'order_by' => 'name',
                'order_direction' => 'asc',
                'limit' => 25,
            ],
        ],
    ],
];
