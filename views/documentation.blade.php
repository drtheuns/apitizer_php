<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>API documentation</title>

    <style>
     * {
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
     }
     .sidebar {
       width: 16rem;
       background: #2d3748;
       min-height: 100%;
       padding: 2rem 0.5rem 2rem 0.5rem;
       display: flex;
       flex-direction: column;
       color: white;
     }
     .content{
       display: flex;
       flex-direction: column;
       flex-grow: 1;
       padding: 2rem 2rem;
     }
     .sidebar .title {
       display: inline-flex;
       justify-content: center;
       font-weight: 700;
       padding: 1rem 0;
       font-size: 1.1rem;
     }
     .link {
       margin-bottom: 0.5rem;
       padding: 0.5rem 0.75rem;
       display: block;
       border-radius: .25rem;
       color: white;
       text-decoration: none;
     }
     .link:hover {
       background-color: #4a5568;
     }
     h2.section-title {
       border-bottom: 1px solid #AAAAAA;
       padding-bottom: 1.2rem;
     }
     .resource {
       margin-bottom: 2rem;
     }
     .icon-link {
       opacity: 0;
       transition: opacity .2s ease-in-out;
       display: inline-block;
       text-decoration: none;
     }
     .icon-link svg {
       color: #666;
       fill: currentColor;
       width: 1rem;
     }
     .icon-link:hover {
       opacity: 1;
     }
     h2 .icon-link svg {
       width: 1.2rem;
     }
     table {
       table-layout: auto;
       border-collapse: collapse;
     }
     table thead tr th, table tbody tr td {
       padding: .5rem 1rem;
       text-align: left;
       font-family: inherit;
     }
     table tbody tr:nth-child(even) {
       background: #EEEEEE;
     }
     table tbody tr:hover {
       background: #CCCCCC;
     }
     .font-monospaced {
       font-family: Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
     }
     .font-sans {
       font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
     }
    </style>
  </head>
  <body>
    <div class="wrapper">
      <nav class="sidebar">
        <header class="title">
          <span>Resources</span>
        </header>
        <ul class="resources">
          @foreach ($docs as $doc)
            <li>
              <a class="link" href="#{{ $doc->getName() }}">{{ $doc->getName() }}</a>
            </li>
          @endforeach
        </ul>
      </nav>
      <main class="content">
        @foreach ($docs as $doc)
          <section class="resource">
            @include('apitizer::section_link', ['heading' => 2, 'title' => $doc->getName(), 'anchor' => $doc->getName()])
            <section>
              @include('apitizer::section_link', ['heading' => 3, 'title' => 'Fields', 'anchor' => $doc->getAnchorName('fields')])
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
            </section>
            <section>
              @include('apitizer::section_link', ['heading' => 3, 'title' => 'Associations', 'anchor' => $doc->getAnchorName('associations')])
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
                      <td>{{ $docs->printAssociationType($assoc) }}</td>
                      <td>{{ $assoc->getDescription() }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </section>
          </section>
        @endforeach
      </main>
    </div>
  </body>
</html>
