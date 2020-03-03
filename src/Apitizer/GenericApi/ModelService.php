<?php

namespace Apitizer\GenericApi;

use Apitizer\QueryBuilder;
use Illuminate\Database\Eloquent\Model;

class ModelService implements Service
{
    public function create(array $validated, QueryBuilder $queryBuilder): Model
    {
        $newModel = $queryBuilder->model();
        $newModel->fill($validated);
        $newModel->save();

        return $newModel;
    }

    public function update(Model $model, array $validated, QueryBuilder $queryBuilder): Model
    {
        $model->fill($validated);
        $model->save();

        return $model;
    }

    public function delete(Model $model, QueryBuilder $queryBuilder): void
    {
        $model->delete();
    }
}
