<?php

namespace Apitizer;

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
     * Set the query builder that is currently being executed.
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder): QueryableDataSource;

    /**
     * Limit the fetched data from the data source based on the filters that
     * were passed in this array.
     *
     * This is called before `fetchData`.
     */
    public function applyFilters(array $filters): QueryableDataSource;

    /**
     * Sort the resultset from the data source based on the sorting options.
     *
     * This is called after applyFilters, before applySelect.
     */
    public function applySorting(array $sorting): QueryableDataSource;

    /**
     * Limit the selection of data from the data source to only the fields given
     * in this request.
     *
     * This is called before, after the filters and sorts `fetchData`.
     *
     * In the database example given above, this would be the select fields on
     * the model.
     *
     * @param array $fields the fields that were parsed from the request. Each
     * field is either a QueryBuilder\Field or QueryBuilder\Association
     */
    public function applySelect(array $fields): QueryableDataSource;


    /**
     * Fetch the data from the actual source.
     *
     * In the database example this would be executing the actual query.
     */
    public function fetchData(): iterable;
}
