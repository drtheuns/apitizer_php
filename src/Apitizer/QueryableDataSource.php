<?php

namespace Apitizer;

use Apitizer\Types\FetchSpec;

/**
 * A queryable data source is a class that is capable of interpreting a query
 * and applying all the needed actions on that data source to get the desired
 * result.
 *
 * An example could be a database. An adapter could be written that handles all
 * the necessary filtering, sorting and selecting of the data.
 */
interface QueryableDataSource
{
    /**
     * The function responsible for turning a fetch specification into response data.
     */
    public function fetchData(QueryBuilder $queryBuilder, FetchSpec $fetchSpec): iterable;
}
