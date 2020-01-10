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
     * The registered query builders.
     *
     * This should be set by the applications that use this library.
     */
    'query_builders' => [],
];
