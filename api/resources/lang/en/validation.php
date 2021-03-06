<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validations anglaises
    |--------------------------------------------------------------------------
    |
    | Les lignes suivantes contiennent les messages d'erreur utilisés par la
    | classe de validation. Certaines de ces règles possèdent plusieurs versions
    | comme la règles "size".
    |
    */

    'accepted'        => 'The :attribute must be accepted.',
    'active_url'      => 'The :attribute is not a valid URL.',
    'after'           => 'The :attribute must be a date after :date.',
    'after_or_equal'  => 'The :attribute must be a date after or equal to :date.',
    'alpha'           => 'The :attribute may only contain letters.',
    'alpha_dash'      => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num'       => 'The :attribute may only contain letters and numbers.',
    'array'           => 'The :attribute must be an array.',
    'before'          => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between'         => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'        => 'The :attribute field must be true or false.',
    'confirmed'      => 'The :attribute confirmation does not match.',
    'date'           => 'The :attribute is not a valid date.',
    'date_format'    => 'The :attribute does not match the format :format.',
    'different'      => 'The :attribute and :other must be different.',
    'digits'         => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions'     => 'The :attribute has invalid image dimensions.',
    'distinct'       => 'The :attribute field has a duplicate value.',
    'email'          => 'The :attribute must be a valid email address.',
    'exists'         => 'The selected :attribute is invalid.',
    'file'           => 'The :attribute must be a file.',
    'filled'         => 'The :attribute field must have a value.',
    'gt'             => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'string'  => 'The :attribute must be greater than :value characters.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'string'  => 'The :attribute must be greater than or equal :value characters.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'image'    => 'The :attribute must be an image.',
    'in'       => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer'  => 'The :attribute must be an integer.',
    'ip'       => 'The :attribute must be a valid IP address.',
    'ipv4'     => 'The :attribute must be a valid IPv4 address.',
    'ipv6'     => 'The :attribute must be a valid IPv6 address.',
    'json'     => 'The :attribute must be a valid JSON string.',
    'lt'       => [
        'numeric' => 'The :attribute must be less than :value.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'string'  => 'The :attribute must be less than :value characters.',
        'array'   => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
        'string'  => 'The :attribute must be less than or equal :value characters.',
        'array'   => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'     => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min'       => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'   => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique'   => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url'      => 'The :attribute format is invalid.',

    'lower_reserved_place'                  => 'The new number of available places can\'t be lower than the current number of available places.',
    'seat_exist_in_lan_seat_io'             => 'The selected seat id is invalid.',
    'seat_not_arrived_seat_io'              => 'This seat is already set to arrived.',
    'seat_not_booked_seat_io'               => 'This seat is already set to booked.',
    'seat_not_free_seat_io'                 => 'This seat is not associated with a reservation.',
    'seat_once_per_lan'                     => 'This seat is already taken for this event.',
    'seat_once_per_lan_seat_io'             => 'This seat is already taken for this event.',
    'user_once_per_lan'                     => 'The user already has a seat at this event.',
    'valid_event_key'                       => 'The event key is not valid.',
    'one_of_two_fields'                     => 'Field :value can\'t be used if the field :second_field is used too.',
    'many_image_ids_exist'                  => 'The ids :ids on the field :attribute don\'t exist.',
    'seat_lan_relation_exists'              => 'The relation between seat with id :seat_id and LAN with id :lan_id doesn\'t exist.',
    'after_or_equal_lan_start_time'         => 'The tournament start time must be after or equal the lan start time.',
    'before_or_equal_lan_end_time'          => 'The tournament end time must be before or equal the lan end time.',
    'unique_user_per_tournament'            => 'A user can only be once in a tournament.',
    'unique_team_tag_per_tournament'        => 'A team tag must be unique per lan.',
    'unique_team_name_per_tournament'       => 'A team name must be unique per lan.',
    'unique_user_per_request'               => 'A user can only have one request per team.',
    'players_to_reach_lock'                 => 'The players to reach can\'t be changed once users have started registering for the tournament.',
    'valid_facebook_token'                  => 'Invalid Facebook token.',
    'valid_google_token'                    => 'Invalid Google token.',
    'facebook_email_permission'             => 'The email permission must be provided.',
    'unique_email_social_login'             => 'The email has already been taken.',
    'tag_belongs_to_user'                   => 'The tag must belong to the user.',
    'user_belongs_in_team'                  => 'The user must be in the team.',
    'tag_belongs_in_team'                   => 'The tag must be in the team.',
    'tag_not_belongs_leader'                => 'The tag must not belong to the leader of the team.',
    'user_is_team_leader'                   => 'The user user be the team leader.',
    'request_belongs_in_team'               => 'The request must be for the leaders team.',
    'organizer_has_tournament'              => 'The user doesn\'t have any tournaments',
    'array_of_integer'                      => 'The array must contain only integers.',
    'elements_in_array_exist_in_permission' => 'An element of the array is not an existing permission id.',
    'has_permission'                        => 'The user does not have the permission to access the requested resource.',
    'permissions_can_be_per_lan'            => 'One of the provided permissions cannot be attributed to a LAN role.',
    'permissions_dont_belong_to_user'       => 'One of the provided permissions is already attributed to this role.',
    'permissions_belong_to_user'            => 'One of the provided permissions is not attributed to this role.',
    'lan_role_name_once_per_lan'            => 'The name of the LAN role must be unique per LAN.',
    'role_once_per_user'                    => 'The user already has this role.',
    'positive_integer'                      => 'The :attribute must be a positive integer.',
    'request_belongs_to_user'               => 'The request must belong to the user.',
    'email_not_current_user'                => 'The email cannot be the same as the one used by the current user.',
    'forbidden'                             => 'REEEEEEEEEE',

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
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],
];
