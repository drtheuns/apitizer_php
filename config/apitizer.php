<?php

return [
    /*
     * Settings that determine which query parameters should be used for the
     * query builder.
     */
    'query_parameters' => [
        'filters' => 'filters',
        'fields'  => 'fields',
        'sort'    => 'sort',
        'limit'   => 'limit',
    ],

    /*
     * Whether or not Apitizer should generate API documentation based on the
     * query builders.
     */
    'generate_documentation' => true,

    /*
     * The route prefix that should be used for the documentation routes.
     */
    'route_prefix' => 'apidoc',

    /*
     * Common validation settings.
     */
    'validation' => [
        'date_format' => 'Y-m-d',
        'datetime_format' => DATE_ATOM,
    ],

    /*
     * Register the query builders of this project.
     */
    'query_builders' => [
        /*
         * Individual classes can be registered here. Expects the fully qualified namespace.
         */
        'classes' => [
            //
        ],

        /*
         * Register all the query builders from the given namespaces.
         */
        'namespaces' => [
            'App\QueryBuilders',
        ],
    ],
];
