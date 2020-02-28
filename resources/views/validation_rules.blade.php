<div class="object-rules">
  <ul>
    @foreach ($builder->getChildren() as $field)
      <li>
        @include('apitizer::validation_field', ['field' => $field, 'name' => $field->getFieldName()])
      </li>
    @endforeach
  </ul>
</div>
