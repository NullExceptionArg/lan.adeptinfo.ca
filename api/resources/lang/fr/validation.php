<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validations françaises
    |--------------------------------------------------------------------------
    |
    | Les lignes suivantes contiennent les messages d'erreur utilisés par la
    | classe de validation. Certaines de ces règles possèdent plusieurs versions
    | comme la règles "size".
    |
    */

    'accepted'        => 'Le champ :attribute doit être accepté.',
    'active_url'      => "Le champ :attribute n'est pas une URL valide.",
    'after'           => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal'  => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha'           => 'Le champ :attribute doit contenir uniquement des lettres.',
    'alpha_dash'      => 'Le champ :attribute doit contenir uniquement des lettres, des chiffres et des tirets.',
    'alpha_num'       => 'Le champ :attribute doit contenir uniquement des chiffres et des lettres.',
    'array'           => 'Le champ :attribute doit être un tableau.',
    'before'          => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    'between'         => [
        'numeric' => 'La valeur de :attribute doit être comprise entre :min et :max.',
        'file'    => 'La taille du fichier de :attribute doit être comprise entre :min et :max kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir entre :min et :max caractères.',
        'array'   => 'Le tableau :attribute doit contenir entre :min et :max éléments.',
    ],
    'boolean'        => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed'      => 'Le champ de confirmation :attribute ne correspond pas.',
    'date'           => "Le champ :attribute n'est pas une date valide.",
    'date_format'    => 'Le champ :attribute ne correspond pas au format :format.',
    'different'      => 'Les champs :attribute et :other doivent être différents.',
    'digits'         => 'Le champ :attribute doit contenir :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
    'dimensions'     => "La taille de l'image :attribute n'est pas conforme.",
    'distinct'       => 'Le champ :attribute a une valeur en double.',
    'email'          => 'Le champ :attribute doit être une adresse courriel valide.',
    'exists'         => 'Le champ :attribute sélectionné est invalide.',
    'file'           => 'Le champ :attribute doit être un fichier.',
    'filled'         => 'Le champ :attribute doit avoir une valeur.',
    'gt'             => [
        'numeric' => 'La valeur de :attribute doit être supérieure à :value.',
        'file'    => 'La taille du fichier de :attribute doit être supérieure à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir plus de :value caractères.',
        'array'   => 'Le tableau :attribute doit contenir plus de :value éléments.',
    ],
    'gte' => [
        'numeric' => 'La valeur de :attribute doit être supérieure ou égale à :value.',
        'file'    => 'La taille du fichier de :attribute doit être supérieure ou égale à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir au moins :value caractères.',
        'array'   => 'Le tableau :attribute doit contenir au moins :value éléments.',
    ],
    'image'    => 'Le champ :attribute doit être une image.',
    'in'       => 'Le champ :attribute est invalide.',
    'in_array' => "Le champ :attribute n'existe pas dans :other.",
    'integer'  => 'Le champ :attribute doit être un entier.',
    'ip'       => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4'     => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6'     => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json'     => 'Le champ :attribute doit être un document JSON valide.',
    'lt'       => [
        'numeric' => 'La valeur de :attribute doit être inférieure à :value.',
        'file'    => 'La taille du fichier de :attribute doit être inférieure à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir moins de :value caractères.',
        'array'   => 'Le tableau :attribute doit contenir moins de :value éléments.',
    ],
    'lte' => [
        'numeric' => 'La valeur de :attribute doit être inférieure ou égale à :value.',
        'file'    => 'La taille du fichier de :attribute doit être inférieure ou égale à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir au plus :value caractères.',
        'array'   => 'Le tableau :attribute doit contenir au plus :value éléments.',
    ],
    'max' => [
        'numeric' => 'La valeur de :attribute ne peut être supérieure à :max.',
        'file'    => 'La taille du fichier de :attribute ne peut pas dépasser :max kilo-octets.',
        'string'  => 'Le texte de :attribute ne peut contenir plus de :max caractères.',
        'array'   => 'Le tableau :attribute ne peut contenir plus de :max éléments.',
    ],
    'mimes'     => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'min'       => [
        'numeric' => 'La valeur de :attribute doit être supérieure ou égale à :min.',
        'file'    => 'La taille du fichier de :attribute doit être supérieure à :min kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir au moins :min caractères.',
        'array'   => 'Le tableau :attribute doit contenir au moins :min éléments.',
    ],
    'not_in'               => "Le champ :attribute sélectionné n'est pas valide.",
    'not_regex'            => "Le format du champ :attribute n'est pas valide.",
    'numeric'              => 'Le champ :attribute doit contenir un nombre.',
    'present'              => 'Le champ :attribute doit être présent.',
    'regex'                => 'Le format du champ :attribute est invalide.',
    'required'             => 'Le champ :attribute est obligatoire.',
    'required_if'          => 'Le champ :attribute est obligatoire quand la valeur de :other est :value.',
    'required_unless'      => 'Le champ :attribute est obligatoire sauf si :other est :values.',
    'required_with'        => 'Le champ :attribute est obligatoire quand :values est présent.',
    'required_with_all'    => 'Le champ :attribute est obligatoire quand :values est présent.',
    'required_without'     => "Le champ :attribute est obligatoire quand :values n'est pas présent.",
    'required_without_all' => "Le champ :attribute est requis quand aucun de :values n'est présent.",
    'same'                 => 'Les champs :attribute et :other doivent être identiques.',
    'size'                 => [
        'numeric' => 'La valeur de :attribute doit être :size.',
        'file'    => 'La taille du fichier de :attribute doit être de :size kilo-octets.',
        'string'  => 'Le texte de :attribute doit contenir :size caractères.',
        'array'   => 'Le tableau :attribute doit contenir :size éléments.',
    ],
    'string'   => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique'   => 'La valeur du champ :attribute est déjà utilisée.',
    'uploaded' => "Le fichier du champ :attribute n'a pu être téléversé.",
    'url'      => "Le format de l'URL de :attribute n'est pas valide.",

    'lower_reserved_place'                  => 'Le nouveau nombre de places disponibles ne peut être plus bas que le nombre de places prises.',
    'seat_exist_in_lan_seat_io'             => 'L\'id du lan sélectionné est invalide.',
    'seat_not_arrived_seat_io'              => 'Cette place est déjà arrivée.',
    'seat_not_booked_seat_io'               => 'Cette place est déjà réservée.',
    'seat_not_free_seat_io'                 => 'Cette place n\'est pas associée à une réservation.',
    'seat_once_per_lan'                     => 'Cette place est déjà réservée pour cet évenement.',
    'seat_once_per_lan_seat_io'             => 'Cette place est déjà réservée pour cet évenement.',
    'user_once_per_lan'                     => 'Cette utilisateur a déjà une place réservé à cet évenement.',
    'valid_event_key'                       => 'La clée d\'évenement n\'est pas valide.',
    'one_of_two_fields'                     => 'Le champ :value ne peut pas être utilisé si le champ :second_field est aussi utilisé.',
    'many_image_ids_exist'                  => 'Les id :ids sur le champ :attribute n\'existent pas.',
    'seat_lan_relation_exists'              => 'La relation entre la place avec l\id :seat_id et le LAN avec l\'id :lan_id n\'existe pas.',
    'after_or_equal_lan_start_time'         => 'Le début du tournoi doit être après ou en même temps que le moment de début du LAN.',
    'before_or_equal_lan_end_time'          => 'La fin du tournoi doit être avant ou en même temps que le moment de fin du LAN.',
    'unique_user_per_tournament'            => 'Un utilisateur ne peut être qu\'une seule fois dans un tournoi.',
    'unique_team_tag_per_tournament'        => 'Un tag d\'équipe doit être unique par lan.',
    'unique_team_name_per_tournament'       => 'Un nom d\'équipe doit être unique par lan.',
    'unique_user_per_request'               => 'Un utilisateur ne peut avoir qu\'une demande d\'admission par équipe.',
    'players_to_reach_lock'                 => 'Le nombre de joueurs à atteindre ne peut être modifiée une fois que des équipes sont inscrites au tournoi.',
    'valid_facebook_token'                  => 'Token Facebook invalide.',
    'valid_google_token'                    => 'Token Google invalide.',
    'facebook_email_permission'             => 'La permission d\'utiliser l\'adresse courriel doit être fournie.',
    'unique_email_social_login'             => 'Ce courriel est déjà prit.',
    'tag_belongs_to_user'                   => 'Le tag doit appartenir à l\'utilisateur.',
    'user_belongs_in_team'                  => 'L\'utilisateur doit faire parti de l\'équipe.',
    'tag_belongs_in_team'                   => 'Le tag doit faire parti de l\'équipe.',
    'tag_not_belongs_leader'                => 'Le tag ne peut appartenir au chef de l\'équipe.',
    'user_is_team_leader'                   => 'L\'utilisateur doit être le chef de l\'équipe.',
    'request_belongs_in_team'               => 'La requête doit être pour l\'équipe du chef.',
    'organizer_has_tournament'              => 'L\'utilisateur n\'a aucuns tournois',
    'array_of_integer'                      => 'Le tableau doit contenir que des nombres entiers,',
    'elements_in_array_exist_in_permission' => 'Un élément dans le tableau n\'est pas l\'id d\'une permission.',
    'has_permission'                        => 'L\'utilisateur n\'a pas la permission d\'accéder à la ressource demandé.',
    'permissions_can_be_per_lan'            => 'L\'une des permissions fournies ne peut être attribuée à un rôle pour LAN.',
    'permissions_dont_belong_to_user'       => 'L\'une des permissions fournies est déjà attribuée à ce rôle.',
    'permissions_belong_to_user'            => 'L\'une des permissions n\'est pas attribuée à ce rôle.',
    'lan_role_name_once_per_lan'            => 'Le nom du role de LAN doit être unique par LAN.',
    'role_once_per_user'                    => 'L\'utilisateur possède déjà ce rôle.',
    'positive_integer'                      => 'Le champ :attribute doit être un entier positif.',
    'request_belongs_to_user'               => 'La requête doit appartenir à l\'utilisateur.',
    'email_not_current_user'                => 'Le courriel ne peut être le même que celui qui est utilisé par l\'utilisateur courant.',
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

    'attributes' => [
        'name'                  => 'nom',
        'username'              => "nom d'utilisateur",
        'email'                 => 'adresse courriel',
        'first_name'            => 'prénom',
        'last_name'             => 'nom',
        'password'              => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'city'                  => 'ville',
        'country'               => 'pays',
        'address'               => 'adresse',
        'phone'                 => 'téléphone',
        'mobile'                => 'portable',
        'age'                   => 'âge',
        'sex'                   => 'sexe',
        'gender'                => 'genre',
        'day'                   => 'jour',
        'month'                 => 'mois',
        'year'                  => 'année',
        'hour'                  => 'heure',
        'minute'                => 'minute',
        'second'                => 'seconde',
        'title'                 => 'titre',
        'content'               => 'contenu',
        'description'           => 'description',
        'excerpt'               => 'extrait',
        'date'                  => 'date',
        'time'                  => 'heure',
        'available'             => 'disponible',
        'size'                  => 'taille',
    ],
];
