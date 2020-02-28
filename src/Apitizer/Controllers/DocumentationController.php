<?php

namespace Apitizer\Controllers;

use Apitizer\Apitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DocumentationController
{
    /**
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function list(Request $request)
    {
        $apidoc = Apitizer::getQueryBuilderDocumentation();

        if ($request->wantsJson()) {
            // return json response
            // Use a query builder?
        }

        return view('apitizer::documentation', [
            'docs' => $apidoc,
            'appName' => Config::get('app.name', 'Unknown'),
            'fieldKey' => Apitizer::getFieldKey(),
            'filterKey' => Apitizer::getFilterKey(),
            'sortKey' => Apitizer::getSortKey()
        ]);
    }
}
