<details open>
  <summary>
    <div class="object-meta">
      <span class="object-field-name @if ($field->isRequired()) required @endif">{{ $name }}</span>:
      <span class="object-field-type">{{ $field->getType() }}</span>
    </div>
  </summary>
  <ul class="validation-rules">
    @foreach ($field->getRules() as $rule)
      <li>{!! $rule->toHtml() !!}</li>
    @endforeach
  </ul>
  @if ($field instanceof \Apitizer\Validation\ArrayRules)
    @foreach ($field->getChildren() as $elementType)
      @include('apitizer::validation_field', ['field' => $elementType, 'name' => $field->getFieldName() . '.*'])
    @endforeach
  @endif
  @if ($field instanceof \Apitizer\Validation\ObjectRules)
    @include('apitizer::validation_rules', ['builder' => $field])
  @endif
</details>
