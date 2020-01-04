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
     ul {
       list-style: none;
       margin: 0;
       padding: 0;
     }
     p {
       margin: 0;
     }

     :root {
       --text-size: 14px;
       --text-color: #232323;
       --primary-color: #2d3748;
       --darken: rgba(0,0,0,0.1);
       --lighten: rgba(255,255,255,0.1);
       --lighten-lg: rgba(255,255,255,0.3);

       --sidebar-width: 300px;
       --sidebar-background-color: var(--primary-color);
       --sidebar-text-color: white;
       --sidebar-item-hover-color: var(--lighten);
       --sidebar-header-height: 64px;
       --sidebar-header-color: var(--darken);

       --content-background-color: #fefefe;
       --topic-max-width: calc(768px + var(--topic-padding) + var(--topic-padding));
       --topic-padding: 5vw;

       --menu-height: 50px;
       --menu-background-color: var(--sidebar-background-color);
       --menu-text-color: var(--sidebar-text-color);
     }
     .dark-mode {
       --content-background-color: black;
     }

     * {
       font-size: var(--text-size);
       line-height: 1.5;
       box-sizing: border-box;
     }
     *, .font-sans {
       font-family: sans serif;
     }
     code, pre, .font-monospaced {
       font-family: monospace;
     }
     code {
       padding: 2px 5px;
     }
     pre {
       overflow: auto;
       padding: 5px;
       border: 1px solid #cbd5e0;
     }
     code, pre {
       background: #eaeef4;
     }
     .sidebar {
       width: 100%;
       height: 100%;
       position: fixed;
       top: 0;
       left: 0;
       bottom: 0;
       background: var(--sidebar-background-color);
       border-right: 1px solid #DBDBDB;
       color: var(--sidebar-text-color);
       display: none;
       z-index: 99;
     }
     .sidebar-header {
       position: absolute;
       top: 0;
       left: 0;
       height: var(--sidebar-header-height);
       width: 100%;
       background: var(--sidebar-header-color);
       display: none;
     }
     .sidebar-title {
       padding: 20px;
       display: flex;
       flex-direction: row;
       align-items: center;
       justify-content: space-between;
       font-weight: 700;
       font-size: 16px;
     }
     .sidebar-content {
       overflow-y: auto;
       height: 100%;
       position: absolute;
       top: var(--sidebar-header-height);
       left: 0;
       bottom: 0;
       width: 100%;
       padding-top: 20px;
     }
     .sidebar-items {
       padding-bottom: 12px;
     }
     .sidebar-nav-item {
       margin-top: 2px;
       padding: 4px 20px;
       text-decoration: none;
       display: block;
       cursor: pointer;
       color: inherit;
     }
     .sidebar-nav-item:hover {
       background-color: var(--sidebar-item-hover-color);
     }
     .sidebar-nav-heading {
       font-weight: 700;
       margin-top: 20px;
       padding: 4px 20px;
       display: block;
     }
     .content {
       position: absolute;
       top: var(--menu-height);
       right: 0;
       left: 0;
       bottom: 0;
       overflow: hide;
       background: var(--content-background-color);
       box-sizing: border-box;
       outline: none;
       max-width: 100vw;
     }
     .topic {
       width: 100%;
       display: flex;
       flex-direction: column;
       align-items: center;
     }
     .topic-content {
       width: 100%;
       max-width: var(--topic-max-width);
       padding: var(--topic-padding);
     }
     .topic-title {
       font-size: 24px;
     }
     .content > * ~ * {
       border-top: 2px solid #dedede;
     }
     .attributes-title, assoc-title {
       font-size: 18px;
     }
     .attribute-name, .assoc-name {
       font-weight: 700;
     }
     .attribute-type, .attribute-name {
       font-family: monospace;
     }
     .attribute {
       border-top: 1px solid #dedede;
       padding: 10px 0;
       font-size: 14px;
     }
     .attribute-short {
       display: flex;
       flex-direction: row;
       flex-wrap: wrap;
       justify-content: space-between;
       flex: 1 0 auto;
       margin-top: -15px;
     }
     .attribute-short > div {
       margin-top: 15px;
     }
     .attribute-description {
       margin-top: 10px;
     }
     .topic-content > .topic-section ~ .topic-section {
       margin-top: 40px;
     }
     .menu {
       position: fixed;
       top: 0;
       left: 0;
       right: 0;
       height: var(--menu-height);
       z-index: 100;
       background-color: var(--menu-background-color);
       color: var(--menu-text-color);
     }
     .menu-inner {
       display: flex;
       flex-direction: row;
       justify-content: space-between;
       height: 100%;
       align-items: center;
     }
     .menu-title {
       font-weight: 700;
       font-size: 16px;
       padding-left: 20px;
     }
     .menu-open svg {
       width: 20px;
       fill: currentColor;
       color: var(--menu-text-color);
     }
     .menu-open {
       padding: 0 24px;
       height: 100%;
     }
     .menu-open:hover {
       background-color: var(--lighten);
     }
     .menu-open:focus {
       outline: 0;
     }
     .open {
       display: initial;
     }
     h2::before {
       display: block;
       content: " ";
       margin-top: -100px;
       height: 100px;
       visibility: hidden;
       pointer-events: none;
     }

     @media (min-width: 768px) {
       .content {
         top: 0;
         left: var(--sidebar-width);
       }
       .sidebar {
         width: var(--sidebar-width);
         display: initial;
       }
       .sidebar-header {
         display: initial;
       }
       .menu {
         display: none;
       }
     }
    </style>
  </head>
  <body class="">
    <header class="menu">
      <div class="menu-inner">
        <span class="menu-title">{{ $appName }}</span>
        <button class="menu-open" aria-label="show navigation menu" id="menu-toggle">
          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
        </button>
      </div>
    </header>
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-title">
          <span>{{ $appName }}</span>
          <button class="dark-mode-toggle">X</button>
        </div>
      </div>
      <nav class="sidebar-content" role="navigation">
        <ul class="sidebar-items">
          <li><a href="#fields" class="sidebar-nav-item">Fields</a></li>
          <li><a href="#filtering" class="sidebar-nav-item">Filtering</a></li>
          <li><a href="#sorting" class="sidebar-nav-item">Sorting</a></li>
          <span class="sidebar-nav-heading">Resources</span>
          @foreach ($docs as $doc)
            <li>
              <a class="sidebar-nav-item" href="#{{ $doc->getName() }}">{{ $doc->getName() }}</a>
            </li>
          @endforeach
        </ul>
      </nav>
    </aside>
    <main class="content" tabindex="0">
      <section class="topic">
        <div class="topic-content">
          <h2 id="fields" class="topic-title">Fields</h2>
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
          fields and none of the associations will be returned.
        </div>
      </section>
      <section class="topic">
        <div class="topic-content">
          <h2 id="filtering" class="topic-title">Filtering</h2>
          <p>
            Filtering can be applied to index calls. The available filters
            and how they work differ per filter and should be documented for
            each filter. Applying a filter can be done as follows:
            <pre>
/posts?{{ $filterKey }}[search]=api&{{ $filterKey }}[published_after]=2019-06-01</pre>
          </p>
        </div>
      </section>
      <section class="topic">
        <div class="topic-content">
          <h2 id="sorting" class="topic-title">Sorting</h2>
          <p>
            Sorting allows the response to be sorted either ascending or
            descending. Similar to filtering, sorting differs from resource
            to resource and the documentation should therefore be consulted
            to find the available sorting keys. Syntax is as follows:
            <pre>
/posts?sort=name
/posts?sort=name.asc
/users?sort=first_name.desc,last_name.asc
/users?sort[]=first_name.desc&sort[]=last_name.asc</pre>
            The first two examples are the same, as ascending order is the
            default ordering method. The last two examples are also the same,
            but uses an array of sorting, rather than a single string.
          </p>
        </div>
      </section>
      </section>
      @foreach ($docs as $doc)
        @include('apitizer::resource_section', ['doc' => $doc])
      @endforeach
    </main>
    <script>
     document.getElementById("menu-toggle").addEventListener("click", function (e) {
       document.getElementById("sidebar").classList.toggle('open');
     });

     document.getElementById("sidebar").addEventListener("click", function (e) {
       if (e.target.tagName.toLowerCase() === "a") {
         document.getElementById("sidebar").classList.remove('open');
       }
     });
    </script>
  </body>
</html>
