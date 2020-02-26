<?php

return [
    'accepted'             => 'The value must be a "true" boolean value: yes, on, 1, or true',
    'active_url'           => 'The value must have a valid A or AAAA record',
    'after'                => 'The value must be after :date',
    'after_or_equal'       => 'The value must be equal to or after :date',
    'alpha'                => 'The value must consist entirely of alphabetic characters',
    'alpha_dash'           => 'The value must consist entirely of alpha-numeric characters',
    'before'               => 'The value must be before :date',
    'before_or_equal'      => 'The value must be equal to or before :date',
    'between'              => 'The value must be between :min and :max',
    'confirmed'            => 'There must be a field :field with the same value',
    'date_equals'          => 'The date must be equal to :date',
    'different'            => 'The value must be different from the value in field :field',
    'digits'               => 'The value must be exactly :length digits long',
    'digits_between'       => 'The value must be between :min and :max digits long',
    'dimensions'           => 'The image must abide by the following constraints: :constraints',
    'distinct'             => 'The array may not contain duplicate values',
    'email'                => 'The value must be valid email',
    'ends_with'            => 'The value must end with: :values',
    'exists'               => 'The value must exist on the server side',
    'image'                => 'The file must be an image',
    'filled'               => 'The value may not be empty if present',
    'gt'                   => 'The value must be greater than the value in :field',
    'gte'                  => 'The value must be greater than or equal to the value in :field',
    'lt'                   => 'The value must be less than the value in :field',
    'lte'                  => 'The value must be less than or equal to the value in :field',
    'in'                   => 'The value must be one of: `[:values]`',
    'in_array'             => 'The value must be in the array of field :field',
    'ip'                   => 'The value must be a valid IP address',
    'ipv4'                 => 'The value must be a valid IPv4 address',
    'ipv6'                 => 'The value must be a valid IPv6 address',
    'json'                 => 'The value must be a valid json string',
    'max'                  => 'The value must be less than or equal to :max',
    'min'                  => 'The value must be greater than or equal to :min',
    'mimetypes'            => 'The file must have one of the following mime types: :values',
    'mimes'                => 'The file must have one of the following mimes: :values',
    'not_in'               => 'The value must not be included in: :values',
    'not_regex'            => 'The value must not match the regex: :regex',
    'nullable'             => 'The value may be nullable',
    'present'              => 'The field must be present',
    'regex'                => 'The value must match the regex: :regex',
    'required_unless'      => 'The value is required unless :field has value: :value',
    'required_if'          => 'The field is required if: :reason',
    'required_with'        => 'The field is required only if :fields are present',
    'required_with_all'    => 'The field is required only if all of :fields are present',
    'required_without'     => 'The field is required only when :fields are not present',
    'required_without_all' => 'The field is required only when :fields are not present',
    'same'                 => 'The value must be the same as the value of field :field',
    'size'                 => 'The value must have a matching size with :size',
    'starts_with'          => 'The value must start with: :values',
    'timezone'             => 'The value must be a valid timezone (e.g. Europe/Amsterdam)',
    'unique'               => 'The value must be a unique resource on the server',
    'url'                  => 'The value must be a valid url',
    'uuid'                 => 'The value must be a valid UUID',
];
