<?php

namespace Apitizer\Controllers;

use Illuminate\Http\Request;

use Apitizer\Apitizer;
use Apitizer\Types\Apidoc;
use Apitizer\Types\ApidocCollection;
use Apitizer\QueryBuilder;

class DocumentationController
{
    public function list(Request $request)
    {
        $builders = Apitizer::getQueryBuilders();

        $apidoc = array_map(function ($builderClass) use ($request) {
            return new Apidoc(new $builderClass($request));
        }, $builders);

        if ($request->wantsJson()) {
            // return json response
            // Use a query builder?
        }

        return view('apitizer::documentation', [
            'docs' => new ApidocCollection($apidoc),
        ]);
    }
}
