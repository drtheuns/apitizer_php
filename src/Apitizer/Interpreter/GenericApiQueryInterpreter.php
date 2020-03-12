<?php

namespace Apitizer\Interpreter;

use Apitizer\GenericApi\RouteParameter;
use Apitizer\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * This interpreter is used in the generic API controller to add the route
 * parameter relationships to the query.
 */
class GenericApiQueryInterpreter extends QueryInterpreter
{
    /**
     * @var RouteParameter[]
     */
    protected $routeParameters;

    /**
     * @param RouteParameter[] $routeParameters
     */
    public function __construct(array $routeParameters)
    {
        $this->routeParameters = $routeParameters;
    }

    protected function newQueryInstance(QueryBuilder $queryBuilder): Builder
    {
        if (empty($this->routeParameters)) {
            return $queryBuilder->model()->query();
        }

        $previousParameter = null;
        $query = null;

        // For each route parameter, we're going to create the appropriate query
        // object. Any subsequent route parameter will then resolve the previous
        // query and continue creating a new query with the association.
        // This way, we'll eventually end up with a query that can be used to
        // resolve the current request.
        foreach ($this->routeParameters as $routeParameter) {
            $schema = $routeParameter->getSchema();
            $value = $routeParameter->getValue();
            $associationName = $routeParameter->getAssociationName();

            // If there is neither a value, nor an association, we're just going
            // to skip the parameter. This can happen, for example, for route
            // parameters like the starting segment:
            // /posts <-- no param, no assoc.
            if (! $associationName && ! $value) {
                continue;
            }

            if ($query) {
                // If the current association is the same as the previous
                // association, we just want to mutate the existing query,
                // as we're dealing with the "show" vs "index" methods:
                // /posts/{post}/comments/{comments} <-- current param "show"
                //               ^^^^^^^^--- previous param "index"
                if ($previousParameter &&
                    $associationName === $previousParameter->getAssociationName() &&
                    empty($previousParameter->getValue())) {

                    $query->where($query->getModel()->getRouteKeyName(), $value);
                    $previousParameter = $routeParameter;
                    continue;
                }

                // Just like regular route binding in the controller, a 404
                // should be returned if any of the parameter fail to resolve.
                $model = $query->firstOrFail();

                // We'll either have an association that can have a value (in
                // the case of associations that return many results), or no
                // value (e.g. belongsTo).
                // /posts/{post}/comments/{comment} <- comment is a value
                // /posts/{post}/author <- author does not have a value.
                if ($value) {
                    $relation = $model->{$associationName}();
                    $relatedModelRouteKey = $relation->getRelated()->getRouteKeyName();

                    $query = $relation->where($relatedModelRouteKey, $value)->getQuery();
                } else {
                    $query = $model->{$associationName}()->getQuery();
                }
            } else {
                $model = $schema->model();

                // The first route parameter should always have a value, and no
                // association, as it's the top-level resource.
                // /posts/{post} <-- this value
                $query = $model->query()->where($model->getRouteKeyName(), $value);
            }

            $previousParameter = $routeParameter;
        }

        if (! $query) {
            return $queryBuilder->model()->query();
        }

        return $query;
    }
}
