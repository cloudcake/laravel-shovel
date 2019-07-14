<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination Links
    |--------------------------------------------------------------------------
    |
    | Enabling this option will add pagination links to the pagination meta
    | data block which contain the URL's to hit to fetch resource data.
    |
    */
    'includePaginationLinks' => false,

    /*
    |--------------------------------------------------------------------------
    | Omit Empty Object
    |--------------------------------------------------------------------------
    |
    | Enabling this option will omit the data field in responses where the
    | root data object has a value of null. This excludes array data.
    |
    */
    'omitEmptyObject' => false,

    /*
    |--------------------------------------------------------------------------
    | Omit Empty Array
    |--------------------------------------------------------------------------
    |
    | Enabling this option will omit the data field in responses where the
    | root data array has zero items. This excludes empty object data.
    |
    */
    'omitEmptyArray' => true,

    /*
    |--------------------------------------------------------------------------
    | Omit data object on error
    |--------------------------------------------------------------------------
    |
    | Enabling this option will omit the data field in responses where the
    | meta code is not in the OK range (not 200-299).
    |
    */
    'omitDataOnError' => false,
];
