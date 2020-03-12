<?php

return [
    /*
     * Settings that determine which query parameters should be used for the
     * schema.
     */
    'query_parameters' => [
        'filters' => 'filters',
        'fields'  => 'fields',
        'sort'    => 'sort',
        'limit'   => 'limit',
    ],

    /*
     * Whether or not Apitizer should generate API documentation based on the
     * schemas.
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
     * Register the schemas of this project.
     */
    'schemas' => [
        /*
         * Individual classes can be registered here. Expects the fully qualified namespace.
         */
        'classes' => [
            //
        ],

        /*
         * Register all the schemas from the given namespaces.
         */
        'namespaces' => [
            'App\Schemas',
        ],
    ],
];
