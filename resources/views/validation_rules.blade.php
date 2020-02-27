<div class="object-rules">
  <ul>
    @foreach ($builder->getChildren() as $field)
      <li>
        <details open>
          <summary>
            <div class="object-meta">
              <span class="object-field-name @if($field->isRequired()) required @endif">{{ $field->getFieldName() }}</span>:
              <span class="object-field-type">{{ $field->getType() }}</span>
            </div>
          </summary>
          <ul class="validation-rules">
            @foreach ($field->getRules() as $rule)
              <li>{!! $rule->toHtml() !!}</li>
            @endforeach
          </ul>
          @if ($field instanceof \Apitizer\Validation\ObjectRules)
            @include('apitizer::validation_rules', ['builder' => $field])
          @endif
        </details>
      </li>
    @endforeach
  </ul>
</div>
