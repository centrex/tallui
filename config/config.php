<?php

declare(strict_types = 1);

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
        'header_style'     => 'default', // default | minimal | bold | primary

        /**
         * Remember each authenticated user's column-visibility choices
         * across sessions/devices (via the cache store below), instead of
         * only for the current URL. Set false to disable globally; a host
         * table can also opt out via public $persistColumnPreferences = false.
         */
        'persist_column_preferences' => true,
    ],

    /**
     * Theme settings for <x-tallui-theme-toggle> (binary light/dark) and
     * <x-tallui-theme-switcher> (picker across any number of DaisyUI themes).
     *
     * Each option's `mode` (light|dark) drives the Tailwind `dark` class
     * toggle on <html> alongside the DaisyUI `data-theme` attribute, so
     * dark-mode-only styling keeps working no matter which named theme
     * (e.g. "dracula", "winter") is active.
     */
    'theme' => [
        'default' => 'light',
        'options' => [
            ['name' => 'light', 'label' => 'Light', 'mode' => 'light'],
            ['name' => 'cupcake', 'label' => 'Cupcake', 'mode' => 'light'],
            ['name' => 'corporate', 'label' => 'Corporate', 'mode' => 'light'],
            ['name' => 'winter', 'label' => 'Winter', 'mode' => 'light'],
            ['name' => 'emerald', 'label' => 'Emerald', 'mode' => 'light'],
            ['name' => 'dark', 'label' => 'Dark', 'mode' => 'dark'],
            ['name' => 'dracula', 'label' => 'Dracula', 'mode' => 'dark'],
            ['name' => 'night', 'label' => 'Night', 'mode' => 'dark'],
            ['name' => 'synthwave', 'label' => 'Synthwave', 'mode' => 'dark'],
        ],
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
     * Default DaisyUI 5 style modifier for color-filled components that
     * support one (alert, badge, button). Applied whenever a call site
     * doesn't pass an explicit style/type override.
     *
     *      default => 'soft'     <x-tallui-badge type="success"> -> badge-success badge-soft
     *      default => 'solid'    <x-tallui-badge type="success"> -> badge-success (DaisyUI's plain fill, no modifier)
     *
     * One of: 'soft' | 'outline' | 'dash' | 'solid'.
     */
    'style' => [
        'default' => 'soft',
    ],

    /**
     * Form component settings.
     */
    'forms' => [
        'size'              => 'md',
        'search_source_ttl' => 1800,
        'searchable_models' => [
            'user' => [
                'model'           => App\Models\User::class,
                'label'           => 'name',
                'value'           => 'id',
                'search_columns'  => ['name', 'email'],
                'order_by'        => 'name',
                'order_direction' => 'asc',
                'limit'           => 25,
            ],
        ],
    ],

    /**
     * Components settings
     */
    'components' => [
        'spotlight' => [
            'class' => 'App\Support\Spotlight',
        ],
    ],
];
