<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>{{ $appName }} - API documentation</title>

    <style>
     /* Normalize CSS */
     html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}main{display:block}h1{font-size:2em;margin:.67em 0}hr{box-sizing:content-box;height:0;overflow:visible}pre{font-family:monospace,monospace;font-size:1em}a{background-color:transparent}abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}b,strong{font-weight:bolder}code,kbd,samp{font-family:monospace,monospace;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}img{border-style:none}button,input,optgroup,select,textarea{font-family:inherit;font-size:100%;line-height:1.15;margin:0}button,input{overflow:visible}button,select{text-transform:none}button,[type="button"],[type="reset"],[type="submit"]{-webkit-appearance:button}button::-moz-focus-inner,[type="button"]::-moz-focus-inner,[type="reset"]::-moz-focus-inner,[type="submit"]::-moz-focus-inner{border-style:none;padding:0}button:-moz-focusring,[type="button"]:-moz-focusring,[type="reset"]:-moz-focusring,[type="submit"]:-moz-focusring{outline:1px dotted ButtonText}fieldset{padding:.35em .75em .625em}legend{box-sizing:border-box;color:inherit;display:table;max-width:100%;padding:0;white-space:normal}progress{vertical-align:baseline}textarea{overflow:auto}[type="checkbox"],[type="radio"]{box-sizing:border-box;padding:0}[type="number"]::-webkit-inner-spin-button,[type="number"]::-webkit-outer-spin-button{height:auto}[type="search"]{-webkit-appearance:textfield;outline-offset:-2px}[type="search"]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}details{display:block}summary{display:list-item}template{display:none}[hidden]{display:none}
     button {
       background: none;
       border: none;
     }

     :root {
       --text-size: 1.0rem;
       --text-color: white;
       --primary-color: #2d3748;
       --link-hover-color: #4a5568;
       --permalink-color: #666;
       --table-row-alternating-color: #EEEEEE;
       --table-row-hover-color: #CCCCCC;
       --code-background-color: #e2e8f0;
       --code-border-color: #cbd5e0;
     }

     * {
       font-size: var(--text-size);
       line-height: 1.5;
     }

     .font-monospaced, code, pre {
       font-family: Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
     }

     *, .font-sans {
       font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
     }
     body {
       margin: 0;
       height: 100vh;
     }
     ol, ul {
       list-style: none;
       margin: 0;
       padding: 0;
     }
     .wrapper {
       min-height: 100%;
       display: flex;
       flex-direction: column;
     }
     .sidebar {
       background: var(--primary-color);
       padding: 0 2rem;
       min-height: 100%;
       display: flex;
       flex-direction: column;
       color: var(--text-color);
       overflow: auto;
       position: fixed;
       left: 0;
       right: 0;
       display: none;
       margin-top: 56px;
     }
     .content-wrapper {
       margin-left: 0;
     }
     .content{
       display: flex;
       flex-direction: column;
       padding: 1rem 1rem;
       width: auto;
       margin: 0 auto;
     }
     .sidebar .title {
       display: block;
       justify-content: center;
       font-weight: 700;
       padding: 1rem 0;
       font-size: 1.1rem;
       text-align: center;
       text-decoration: none;
       color: var(--text-color);
     }
     .link {
       margin-bottom: 0.5rem;
       padding: 0.5rem 0.75rem;
       display: block;
       border-radius: .25rem;
       color: var(--text-color);
       text-decoration: none;
     }
     .link:hover {
       background-color: var(--link-hover-color);
     }
     h1 {
       font-size: 2rem;
       text-align: center;
     }
     h2 {
       border-bottom: 3px solid var(--primary-color);
       margin-bottom: 2rem;
       padding-bottom: 1rem;
       font-size: 1.7rem;
     }
     h3.section-title {
       font-size: 1.5rem;
       margin-top: 0;
     }
     .icon-link {
       opacity: 0;
       transition: opacity .2s ease-in-out;
       display: inline-block;
       text-decoration: none;
     }
     .icon-link svg {
       color: var(--permalink-color);
       fill: currentColor;
       width: 1rem;
     }
     .icon-link:hover {
       opacity: 1;
     }
     h3 .icon-link svg {
       width: 1.2rem;
     }
     table {
       table-layout: auto;
       border-collapse: collapse;
       width: 100%;
     }
     table thead tr th, table tbody tr td {
       padding: .5rem 1rem;
       text-align: left;
       font-family: inherit;
     }
     table thead tr th {
       font-weight: 600;
       text-transform: uppercase;
       font-size: 0.7rem;
       border-bottom: 2px solid var(--primary-color);
     }
     table tbody tr:nth-child(even) {
       background: var(--table-row-alternating-color);
     }
     table tbody tr:hover {
       background: var(--table-row-hover-color);
     }
     code {
       padding: 0.25rem 0.4rem;
     }
     pre {
       overflow: auto;
       padding: .5rem;
     }
     code, pre {
       background: #e2e8f0;
       border: 2px solid #cbd5e0;
     }
     .resource {
       margin-bottom: 3rem;
     }
     .menu {
       background: var(--primary-color);
       color: var(--text-color);
       position: sticky;
       top: 0;
       left: 0;
       right: 0;
     }
     .menu .inner {
       display: flex;
       flex-direction: row;
       justify-content: space-between;
       padding: 0 0 0 2rem;
     }
     .menu-open {
       padding: 0 1.5rem;
     }
     .menu-open svg {
       width: 1.2rem;
       fill: currentColor;
       color: var(--text-color);
     }
     .menu-open:hover {
       background-color: var(--link-hover-color);
       cursor: pointer;
     }
     .overflow {
       overflow: auto;
     }
     .open {
       display: initial;
     }
     @media (min-width: 640px) {
     :root {
       --text-size: 1.1rem;
     }
     .content-wrapper {
       margin-left: 16rem;
     }
     .content {
       padding: 2rem 2rem;
       width: 48rem;
     }
     .menu {
       display: none;
     }
     .sidebar {
       display: initial;
       width: 16rem;
       padding: 2rem 0.5rem 2rem 0.5rem;
       margin-top: 0;
     }
     }
    </style>
  </head>
  <body>
    <div class="wrapper">
      <header class="menu">
        <div class="inner">
          <p>
            {{ $appName }}
          </p>
          <button class="menu-open" aria-label="Show navigation menu" id="menu-toggle">
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
          </button>
        </div>
      </header>
      <nav class="sidebar" id="sidebar">
        <ul class="resources">
          <a href="#usage" class="title">Usage</a>
          <li><a class="link" href="#fields">Fields</a></li>
          <li><a class="link" href="#filtering">Filtering</a></li>
          <li><a class="link" href="#sorting">Sorting</a></li>
          <a href="#resources" class="title">Resources</a>
          @foreach ($docs as $doc)
            <li>
              <a class="link" href="#{{ $doc->getName() }}">{{ $doc->getName() }}</a>
            </li>
          @endforeach
        </ul>
      </nav>
      <main class="content-wrapper">
        <div class="content">
          <h1>{{ $appName }} - API documentation</h1>
          <section>
            <h2><a id="usage"></a>Usage</h2>
            <p>
              This section describes how to consume this API. There are a couple
              concepts that give the client more control over the response they
              receive from the server, such as selecting specific fields —
              including related models, applying filters, and sorting. These
              functions are outlined in the sections below.
            </p>
            <section class="resource">
              @include('apitizer::section_link', ['heading' => 3, 'title' => 'Fields', 'anchor' => 'fields'])
              <p>
                Specific fields can be requested by using the <code>{{ $fieldKey
                                                                    }}</code> query parameter.
                The fields and associations that can be requested are
                documentated for each resource. The syntax (and response) look
                like this:
              </p>
              <pre>
/users?{{ $fieldKey }}=id,name,posts(id,title,comments(id,body))
{
  "id": 1,
  "name": "John Doe",
  "posts": [
    {
      "id": 1,
      "title": "How to query this API?",
      "comments": [
        {
          "id": 1,
          "body": "This article made no sense to me"
        }
      ]
    }
  ]
}</pre>
              When the <code>fields</code> parameter is left out, all the
              fields and no associations will be returned.
            </section>
            <section class="resource">
              @include('apitizer::section_link', ['heading' => 3, 'title' => 'Filtering', 'anchor' => 'filtering'])
              <p>
                Filtering can be applied to index calls. The available filters
                and how they work differ per filter and should be documented for
                each filter. Applying a filter can be done as follows:
                <pre>
/posts?{{ $filterKey }}[search]=api&{{ $filterKey }}[published_after]=2019-06-01</pre>
              </p>
            </section>
            <section class="resource">
              @include('apitizer::section_link', ['heading' => 3, 'title' => 'Sorting', 'anchor' => 'sorting'])
              <p>
                Sorting allows the response to be sorted either ascending or
                descending. Similar to filtering, sorting differs from resource
                to resource and the documentation should therefore be consulted
                to find the available sorting keys. Syntax is as follows:
                <pre>
/posts?sort=name
/posts?sort=name.asc
/posts?sort=published_at.desc</pre>
                The first two examples are the same, as ascending order is the
                default ordering method.
              </p>
            </section>
          </section>

          <h2><a id="resources"></a>Resources</h2>
          @foreach ($docs as $doc)
            <section class="resource">
              @include('apitizer::section_link', ['heading' => 3, 'title' => $doc->getName(), 'anchor' => $doc->getName()])
              @if($doc->getDescription())
                <p>{{ $doc->getDescription() }}</p>
              @endif
              <section>
                @include('apitizer::section_link', ['heading' => 4, 'title' => 'Fields', 'anchor' => $doc->getAnchorName('fields')])
                <div class="overflow">
                  <table>
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Nullable?</th>
                        <th>Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($doc->getFields() as $field)
                        <tr class="font-monospaced">
                          <td>{{ $field->getName() }}</td>
                          <td>{{ $field->getType() }}</td>
                          <td>{{ $field->isNullable() ? 'Yes' : 'No' }}</td>
                          <td class="font-sans">{{ $field->getDescription() }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </section>
              @if($doc->hasAssociations())
                <section>
                  @include('apitizer::section_link', ['heading' => 4, 'title' => 'Associations', 'anchor' => $doc->getAnchorName('associations')])
                  <div class="overflow">
                    <table>
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Type</th>
                          <th>Description</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($doc->getAssociations() as $assoc)
                          <tr class="font-monospaced">
                            <td>{{ $assoc->getName() }}</td>
                            <td>
                              <a href="#{{ $docs->findAssociationType($assoc)->getName() }}">
                                {{ $docs->printAssociationType($assoc) }}
                              </a>
                            </td>
                            <td class="font-sans">{{ $assoc->getDescription() }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </section>
              @endif
              @if($doc->hasFilters())
                <section>
                  @include('apitizer::section_link', ['heading' => 4, 'title' => 'Filters', 'anchor' => $doc->getAnchorName('filters')])
                  <div class="overflow">
                    <table>
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Input type</th>
                          <th>Description</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($doc->getFilters() as $filter)
                          <tr class="font-monospaced">
                            <td>{{ $filter->getName() }}</td>
                            <td>{{ $filter->getInputType() }}</td>
                            <td class="font-sans">{{ $filter->getDescription() }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </section>
              @endif
              @if($doc->hasSorts())
                <section>
                  @include('apitizer::section_link', ['heading' => 4, 'title' => 'Sorting', 'anchor' => $doc->getAnchorName('sorting')])
                  <div class="overflow">
                    <table>
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Description</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($doc->getSorts() as $sort)
                          <tr class="font-monospaced">
                            <td>{{ $sort->getName() }}</td>
                            <td class="font-sans">{{ $sort->getDescription() }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </section>
              @endif
            </section>
          @endforeach
        </div>
      </main>
    </div>
    <script>
     document.getElementById("menu-toggle").addEventListener("click", function (e) {
       document.getElementById("sidebar").classList.toggle('open');
     });
    </script>
  </body>
</html>
