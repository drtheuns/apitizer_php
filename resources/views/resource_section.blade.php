<section class="topic">
  <div class="topic-content">
    <h2 class="topic-title" id="{{ $doc->getName() }}">
      {{ $doc->getName() }}
    </h2>
    @if ($doc->getDescription())
      <p>{{ $doc->getDescription() }}</p>
    @endif

    <section class="topic-section">
      <h3 class="attribute-title">Attributes</h3>
      <ul>
        @foreach ($doc->getFields() as $field)
          <li class="attribute">
            <div class="attribute-short">
              <div>
                <span class="attribute-name">{{ $field->getName() }}:</span>
                <span class="attribute-type">{{ $field->printType() }}</span>
              </div>
              @if ($field instanceof \Apitizer\Types\EnumField)
                <div class="enum-values">
                  @foreach ($field->getEnum() as $enumVal)
                    <code>{{ $enumVal }}</code>
                  @endforeach
                </div>
              @endif
            </div>
            @if ($field->getDescription())
              <p class="attribute-description">{{ $field->getDescription() }}</p>
            @endif
          </li>
        @endforeach
      </ul>
    </section>

    @if ($doc->hasAssociations())
      <section class="topic-section">
        <h3 class="attribute-title">Associations</h3>
        <ul>
          @foreach ($doc->getAssociations() as $assoc)
            <li class="attribute">
              <span class="attribute-name">{{ $assoc->getName() }}:</span>
              @if ($relatedDoc = $docs->findAssociationType($assoc))
                <a class="attribute-type link" href="#{{ $relatedDoc->getName() }}">
                  {{ $relatedDoc->printAssociationType($assoc) }}
                </a>
              @else
                <span>Documentation is missing</span>
              @endif
              @if ($assoc->getDescription())
                <p class="attribute-description">{{ $assoc->getDescription() }}</p>
              @endif
            </li>
          @endforeach
        </ul>
      </section>
    @endif

    @if ($doc->hasFilters())
      <section class="topic-section">
        <h3 class="attribute-title">Filters</h3>
        <ul>
          @foreach ($doc->getFilters() as $filter)
            <li class="attribute">
              <span class="attribute-name">{{ $filter->getName() }}:</span>
              <span class="attribute-type">{{ $filter->getInputType() }}</span>
              @if ($filter->getDescription())
                <p class="attribute-description">{{ $filter->getDescription() }}</p>
              @endif
            </li>
          @endforeach
        </ul>
      </section>
    @endif

    @if ($doc->hasSorts())
      <section class="topic-section">
        <h3 class="attribute-title">Sorting</h3>
        <ul>
          @foreach ($doc->getSorts() as $sort)
            <li class="attribute">
              <span class="attribute-name">{{ $sort->getName() }}</span>
              @if ($sort->getDescription())
                <p class="attribute-description">{{ $sort->getDescription() }}</p>
              @endif
            </li>
          @endforeach
        </ul>
      </section>
    @endif
    @if ($doc->hasRules())
      <section class="topic-section">
        <h2 class="endpoints-title">Validation</h2>
        @foreach ($doc->getValidationBuilders() as $actionName => $builder)
          <header>
            <h3 class="endpoint-name">{{ $doc->humanizeActionName($actionName) }}</h3>
          </header>
          <?php $id = \Illuminate\Support\Str::random(); ?>
          <div class="tabs">
            <div class="tab-headings">
              <label for="{{ $id }}-1" class="tab-toggle">Text</label>
              <label for="{{ $id }}-2" class="tab-toggle">Typescript</label>
            </div>
            <div class="tab">
              <input name="{{ $id }}" id="{{ $id }}-1" type="radio" checked />
              <div class="tab-content">
                @include('apitizer::validation_rules', ['builder' => $builder])
              </div>
            </div>
            <div class="tab">
              <input name="{{ $id }}" id="{{ $id }}-2" type="radio" />
              <div class="tab-content">
                @include('apitizer::ts_interface', ['resourceName' => $doc->getName(), 'actionName' => $doc->humanizeActionName($actionName), 'builder' => $builder, 'depth' => 1])
              </div>
            </div>
          </div>
        @endforeach
      </section>
    @endif
  </div>
</section>
