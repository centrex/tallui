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
     *              <x-card />
     *
     *    prefix => 'tallui-'
     *               <x-tallui-button />
     *               <x-tallui-card />
     *
     */
    'prefix' => '',

    /**
     * Default route prefix.
     *
     * Some TallUI components make network request to its internal routes.
     *
     *      route_prefix => ''
     *          - Spotlight: '/tallui/spotlight'
     *          - Editor: '/tallui/upload'
     *          - ...
     *
     *      route_prefix => 'my-components'
     *          - Spotlight: '/my-components/tallui/spotlight'
     *          - Editor: '/my-components/tallui/upload'
     *          - ...
     */
    'route_prefix' => '',

    /**
     * Components settings
     */
    'components' => [
        'spotlight' => [
            'class' => 'App\Support\Spotlight',
        ]
    ]
];
