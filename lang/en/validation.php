<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => t('The :attribute field must be accepted.'),
    'accepted_if'     => t('The :attribute field must be accepted when :other is :value.'),
    'active_url'      => t('The :attribute field must be a valid URL.'),
    'after'           => t('The :attribute field must be a date after :date.'),
    'after_or_equal'  => t('The :attribute field must be a date after or equal to :date.'),
    'alpha'           => t('The :attribute field must only contain letters.'),
    'alpha_dash'      => t('The :attribute field must only contain letters, numbers, dashes, and underscores.'),
    'alpha_num'       => t('The :attribute field must only contain letters and numbers.'),
    'array'           => t('The :attribute field must be an array.'),
    'ascii'           => t('The :attribute field must only contain single-byte alphanumeric characters and symbols.'),
    'before'          => t('The :attribute field must be a date before :date.'),
    'before_or_equal' => t('The :attribute field must be a date before or equal to :date.'),
    'between'         => [
        'array'   => t('The :attribute field must have between :min and :max items.'),
        'file'    => t('The :attribute field must be between :min and :max kilobytes.'),
        'numeric' => t('The :attribute field must be between :min and :max.'),
        'string'  => t('The :attribute field must be between :min and :max characters.'),
    ],
    'boolean'           => t('The :attribute field must be true or false.'),
    'can'               => t('The :attribute field contains an unauthorized value.'),
    'confirmed'         => t('The :attribute field confirmation does not match.'),
    'current_password'  => t('The password is incorrect.'),
    'date'              => t('The :attribute field must be a valid date.'),
    'date_equals'       => t('The :attribute field must be a date equal to :date.'),
    'date_format'       => t('The :attribute field must match the format :format.'),
    'decimal'           => t('The :attribute field must have :decimal decimal places.'),
    'declined'          => t('The :attribute field must be declined.'),
    'declined_if'       => t('The :attribute field must be declined when :other is :value.'),
    'different'         => t('The :attribute field and :other must be different.'),
    'digits'            => t('The :attribute field must be :digits digits.'),
    'digits_between'    => t('The :attribute field must be between :min and :max digits.'),
    'dimensions'        => t('The :attribute field has invalid image dimensions.'),
    'distinct'          => t('The :attribute field has a duplicate value.'),
    'doesnt_end_with'   => t('The :attribute field must not end with one of the following: :values.'),
    'doesnt_start_with' => t('The :attribute field must not start with one of the following: :values.'),
    'email'             => t('The :attribute field must be a valid email address.'),
    'ends_with'         => t('The :attribute field must end with one of the following: :values.'),
    'enum'              => t('The selected :attribute is invalid.'),
    'exists'            => t('The selected :attribute is invalid.'),
    'file'              => t('The :attribute field must be a file.'),
    'filled'            => t('The :attribute field must have a value.'),
    'gt'                => [
        'array'   => t('The :attribute field must have more than :value items.'),
        'file'    => t('The :attribute field must be greater than :value kilobytes.'),
        'numeric' => t('The :attribute field must be greater than :value.'),
        'string'  => t('The :attribute field must be greater than :value characters.'),
    ],
    'gte' => [
        'array'   => t('The :attribute field must have :value items or more.'),
        'file'    => t('The :attribute field must be greater than or equal to :value kilobytes.'),
        'numeric' => t('The :attribute field must be greater than or equal to :value.'),
        'string'  => t('The :attribute field must be greater than or equal to :value characters.'),
    ],
    'image'     => t('The :attribute field must be an image.'),
    'in'        => t('The selected :attribute is invalid.'),
    'in_array'  => t('The :attribute field must exist in :other.'),
    'integer'   => t('The :attribute field must be an integer.'),
    'ip'        => t('The :attribute field must be a valid IP address.'),
    'ipv4'      => t('The :attribute field must be a valid IPv4 address.'),
    'ipv6'      => t('The :attribute field must be a valid IPv6 address.'),
    'json'      => t('The :attribute field must be a valid JSON string.'),
    'lowercase' => t('The :attribute field must be lowercase.'),
    'lt'        => [
        'array'   => t('The :attribute field must have less than :value items.'),
        'file'    => t('The :attribute field must be less than :value kilobytes.'),
        'numeric' => t('The :attribute field must be less than :value.'),
        'string'  => t('The :attribute field must be less than :value characters.'),
    ],
    'lte' => [
        'array'   => t('The :attribute field must not have more than :value items.'),
        'file'    => t('The :attribute field must be less than or equal to :value kilobytes.'),
        'numeric' => t('The :attribute field must be less than or equal to :value.'),
        'string'  => t('The :attribute field must be less than or equal to :value characters.'),
    ],
    'mac_address' => t('The :attribute field must be a valid MAC address.'),
    'max'         => [
        'array'   => t('The :attribute field must not have more than :max items.'),
        'file'    => t('The :attribute field must not be greater than :max kilobytes.'),
        'numeric' => t('The :attribute field must not be greater than :max.'),
        'string'  => t('The :attribute field must not be greater than :max characters.'),
    ],
    'max_digits' => t('The :attribute field must not have more than :max digits.'),
    'mimes'      => t('The :attribute field must be a file of type: :values.'),
    'mimetypes'  => t('The :attribute field must be a file of type: :values.'),
    'min'        => [
        'array'   => t('The :attribute field must have at least :min items.'),
        'file'    => t('The :attribute field must be at least :min kilobytes.'),
        'numeric' => t('The :attribute field must be at least :min.'),
        'string'  => t('The :attribute field must be at least :min characters.'),
    ],
    'min_digits'       => t('The :attribute field must have at least :min digits.'),
    'missing'          => t('The :attribute field must be missing.'),
    'missing_if'       => t('The :attribute field must be missing when :other is :value.'),
    'missing_unless'   => t('The :attribute field must be missing unless :other is :value.'),
    'missing_with'     => t('The :attribute field must be missing when :values is present.'),
    'missing_with_all' => t('The :attribute field must be missing when :values are present.'),
    'multiple_of'      => t('The :attribute field must be a multiple of :value.'),
    'not_in'           => t('The selected :attribute is invalid.'),
    'not_regex'        => t('The :attribute field format is invalid.'),
    'numeric'          => t('The :attribute field must be a number.'),
    'password'         => [
        'letters'       => t('The :attribute field must contain at least one letter.'),
        'mixed'         => t('The :attribute field must contain at least one uppercase and one lowercase letter.'),
        'numbers'       => t('The :attribute field must contain at least one number.'),
        'symbols'       => t('The :attribute field must contain at least one symbol.'),
        'uncompromised' => t('The given :attribute has appeared in a data leak. Please choose a different :attribute.'),
    ],
    'present'              => t('The :attribute field must be present.'),
    'prohibited'           => t('The :attribute field is prohibited.'),
    'prohibited_if'        => t('The :attribute field is prohibited when :other is :value.'),
    'prohibited_unless'    => t('The :attribute field is prohibited unless :other is in :values.'),
    'prohibits'            => t('The :attribute field prohibits :other from being present.'),
    'regex'                => t('The :attribute field format is invalid.'),
    'required'             => t('The :attribute field is required.'),
    'required_array_keys'  => t('The :attribute field must contain entries for: :values.'),
    'required_if'          => t('The :attribute field is required when :other is :value.'),
    'required_if_accepted' => t('The :attribute field is required when :other is accepted.'),
    'required_unless'      => t('The :attribute field is required unless :other is in :values.'),
    'required_with'        => t('The :attribute field is required when :values is present.'),
    'required_with_all'    => t('The :attribute field is required when :values are present.'),
    'required_without'     => t('The :attribute field is required when :values is not present.'),
    'required_without_all' => t('The :attribute field is required when none of :values are present.'),
    'same'                 => t('The :attribute field must match :other.'),
    'size'                 => [
        'array'   => t('The :attribute field must contain :size items.'),
        'file'    => t('The :attribute field must be :size kilobytes.'),
        'numeric' => t('The :attribute field must be :size.'),
        'string'  => t('The :attribute field must be :size characters.'),
    ],
    'starts_with' => t('The :attribute field must start with one of the following: :values.'),
    'string'      => t('The :attribute field must be a string.'),
    'timezone'    => t('The :attribute field must be a valid timezone.'),
    'unique'      => t('The :attribute has already been taken.'),
    'uploaded'    => t('The :attribute failed to upload.'),
    'uppercase'   => t('The :attribute field must be uppercase.'),
    'url'         => t('The :attribute field must be a valid URL.'),
    'ulid'        => t('The :attribute field must be a valid ULID.'),
    'uuid'        => t('The :attribute field must be a valid UUID.'),

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
