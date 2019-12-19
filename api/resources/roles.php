<?php

return [

    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Rôles
    |-------------------------------------------------------------------------------------------------------------------
    |
    | Chaque entrée du tableau représente un rôle par défaut dans l'application. Chaque rôle doivent avoir un nom unique
    | (name). Le champ display_name est le nom d'affichage du rôle, et le champ description est une description plus
    | exhaustive et plus précise de ce que le rôle permet d'accomplir, et à quel genre d'administrateur il devrait être
    | attribué. Finalement, les rôles par défaut doivent contenir une Chaque rôle doit être traduit dans toutes les
    | langues disponibles dans l'application, en l'occurence le français et l'anglais.
    |
    | Les rôles peuvent être attribués à des utilisateurs, et sont des groupes de permissions.
    |
    | Deux types de rôle existent, les rôles de LAN, et les rôles globaux.
    |
    | Rôle global: rôle qui est effectif sur l'ensemble des LANs.
    | Rôle de LAN: rôle qui est effectif uniquement pour un LAN en particulier.
    |
    | La raison de cette séparation dans les permissions est que certaines permissions ne peuvent être utilisées que
    | dans un contexte général, et non par LAN. Par exemple, il ne devrait pas être possible de créer un LAN en tant que
    | rôle qui a des permissions sur un seul LAN.
    |
    | !*! Attention !*! Les noms (name) des rôles sont utilisés dans le code pour chercher les rôles. C'est pourquoi il
    | faut que toujours remplacer toutes les occurences du nom (name) dans le code s'il a à être modifié.
    |
    */

    'global_roles' => [
        [
            'name'            => 'general-admin',
            'en_display_name' => 'General admin',
            'en_description'  => 'Has every permissions (LAN and Global)',
            'fr_display_name' => 'Administrateur général',
            'fr_description'  => 'Possède toutes les permissions (LAN et globales)',
            'permissions'     => include(base_path().'/resources/permissions.php'),
        ],
    ],
    'lan_roles' => [
        [
            'name'            => 'lan-general-admin',
            'en_display_name' => 'LAN General admin',
            'en_description'  => 'Has every permissions for a LAN',
            'fr_display_name' => 'Administrateur général de LAN',
            'fr_description'  => 'Possède toutes les permissions pour un LAN',
            'permissions'     => collect(include base_path().'/resources/permissions.php')
                ->where('can_be_per_lan', true),
        ],
        [
            'name'            => 'seat-admin',
            'en_display_name' => 'Seat admin',
            'en_description'  => 'Can manage places',
            'fr_display_name' => 'Administrateur des places',
            'fr_description'  => 'Peut gérer les places',
            'permissions'     => [
                ['name' => 'confirm-arrival', 'can_be_per_lan' => true],
                ['name' => 'unconfirm-arrival', 'can_be_per_lan' => true],
                ['name' => 'assign-seat', 'can_be_per_lan' => true],
                ['name' => 'unassign-seat', 'can_be_per_lan' => true],
            ],
        ],
        [
            'name'            => 'tournament-admin',
            'en_display_name' => 'Tournament admin',
            'en_description'  => 'Can manage tournaments et les équipes',
            'fr_display_name' => 'Administrateur des tournois',
            'fr_description'  => 'Peut gérer les tournois et les équipes',
            'permissions'     => [
                ['name' => 'create-tournament', 'can_be_per_lan' => true],
                ['name' => 'edit-tournament', 'can_be_per_lan' => true],
                ['name' => 'delete-tournament', 'can_be_per_lan' => true],
                ['name' => 'delete-team', 'can_be_per_lan' => true],
                ['name' => 'add-organizer', 'can_be_per_lan' => true],
                ['name' => 'remove-organizer', 'can_be_per_lan' => true],
            ],
        ],
    ], ];
