{
@foreach ($builder->getChildren() as $field)
{{ str_repeat('  ', $depth) }}{{ $field->getFieldName() }}@if (!$field->isRequired())?@endif: {{ \Apitizer\Support\TsViewHelper::printableType($field, $depth) }};
@endforeach
{{ str_repeat('  ', $depth - 1) }}}