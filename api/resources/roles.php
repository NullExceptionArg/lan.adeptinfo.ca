<?php

return [

    'global_roles' => [
        [
            'name' => 'general-admin',
            'en_display_name' => 'General admin',
            'en_description' => 'Has every permissions (LAN and Global)',
            'fr_display_name' => 'Administrateur général',
            'fr_description' => 'Possède toutes les permissions (LAN et globales)',
            'permissions' => include(base_path() . '/resources/permissions.php')
        ]
    ],
    'lan_roles' => [
        [
            'name' => 'lan-general-admin',
            'en_display_name' => 'LAN General admin',
            'en_description' => 'Has every permissions for a LAN',
            'fr_display_name' => 'Administrateur général de LAN',
            'fr_description' => 'Possède toutes les permissions pour un LAN',
            'permissions' => collect(include(base_path() . '/resources/permissions.php'))->where('can_be_per_lan', true)
        ],
        [
            'name' => 'seat-admin',
            'en_display_name' => 'Seat admin',
            'en_description' => 'Can manage places',
            'fr_display_name' => 'Administrateur des places',
            'fr_description' => 'Peut gérer les places',
            'permissions' => [
                ['name' => 'confirm-arrival', 'can_be_per_lan' => true],
                ['name' => 'unconfirm-arrival', 'can_be_per_lan' => true],
                ['name' => 'assign-seat', 'can_be_per_lan' => true],
                ['name' => 'unassign-seat', 'can_be_per_lan' => true],
                ['name' => 'admin-summary', 'can_be_per_lan' => true]
            ]
        ],
        [
            'name' => 'tournament-admin',
            'en_display_name' => 'Tournament admin',
            'en_description' => 'Can manage tournaments et les équipes',
            'fr_display_name' => 'Administrateur des tournois',
            'fr_description' => 'Peut gérer les tournois et les équipes',
            'permissions' => [
                ['name' => 'create-tournament', 'can_be_per_lan' => true],
                ['name' => 'edit-tournament', 'can_be_per_lan' => true],
                ['name' => 'delete-tournament', 'can_be_per_lan' => true],
                ['name' => 'quit-tournament', 'can_be_per_lan' => true],
                ['name' => 'delete-team', 'can_be_per_lan' => true],
                ['name' => 'admin-summary', 'can_be_per_lan' => true],
                ['name' => 'add-organizer', 'can_be_per_lan' => true]
            ]
        ],
    ]
];