<?php

return [

    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Permissions
    |-------------------------------------------------------------------------------------------------------------------
    |
    | Chaque entrée du tableau représente une permission dans l'application. Chaque permission doit avoir un nom unique.
    | (name) Les permissions permettent de protéger les chemins HTTP dont l'accès doit être restraint à des
    | administrateurs.
    |
    | Par exemple, la création de LAN ou l'ajout de contributeurs ne peut être possible que pour certains utilisateurs.
    | Les permissions peuvent être attribuées aux utilisateurs par le moyen de rôle.
    |
    | Pour en savoir plus sur les rôles, voir le fichier roles.php, qui est vis à vis ce fichier.
    |
    | Les permissions çi-dessous qui ont l'attribut "can_be_per_lan" à false peuvent être associées à des rôles globaux,
    | et à des rôles de LAN, alors que les permissions qui ont l'attribut "can_be_per_lan" à true ne peuvent être
    | associés qu'à des rôles de LAN.
    |
    | !*! Attention !*! Les noms (name) des permissions sont utilisés dans le fichier roles.php pour définir les
    | permissions des rôles par défaut, et dans les contrôleurs pour définir la permission du chemin HTTP. C'est
    | pourquoi il faut que toujours remplacer toutes les occurences du nom (name) dans le code s'il a à être modifié.
    |
    | Les permissions sont aussi affichées dans la documentation sous chaque point d'accès protégé.
    |
    */

    // LAN
    ['name' => 'create-lan', 'can_be_per_lan' => false],
    ['name' => 'set-current-lan', 'can_be_per_lan' => false],
    ['name' => 'edit-lan', 'can_be_per_lan' => true],

    // Contribution
    ['name' => 'create-contribution-category', 'can_be_per_lan' => true],
    ['name' => 'delete-contribution-category', 'can_be_per_lan' => true],
    ['name' => 'create-contribution', 'can_be_per_lan' => true],
    ['name' => 'delete-contribution', 'can_be_per_lan' => true],

    // Seat
    ['name' => 'confirm-arrival', 'can_be_per_lan' => true],
    ['name' => 'unconfirm-arrival', 'can_be_per_lan' => true],
    ['name' => 'assign-seat', 'can_be_per_lan' => true],
    ['name' => 'unassign-seat', 'can_be_per_lan' => true],
    ['name' => 'get-seat-charts', 'can_be_per_lan' => false],

    // LanImage
    ['name' => 'add-image', 'can_be_per_lan' => true],
    ['name' => 'delete-image', 'can_be_per_lan' => true],

    // Tournament
    ['name' => 'create-tournament', 'can_be_per_lan' => true],
    ['name' => 'edit-tournament', 'can_be_per_lan' => true],
    ['name' => 'delete-tournament', 'can_be_per_lan' => true],
    ['name' => 'add-organizer', 'can_be_per_lan' => true],
    ['name' => 'remove-organizer', 'can_be_per_lan' => true],
    ['name' => 'get-all-tournament-for-organizer', 'can_be_per_lan' => true],

    // Team
    ['name' => 'delete-team', 'can_be_per_lan' => true],

    // Lan roles
    ['name' => 'create-lan-role', 'can_be_per_lan' => true],
    ['name' => 'update-lan-role', 'can_be_per_lan' => true],
    ['name' => 'add-permissions-lan-role', 'can_be_per_lan' => true],
    ['name' => 'delete-permissions-lan-role', 'can_be_per_lan' => true],
    ['name' => 'delete-lan-role', 'can_be_per_lan' => true],
    ['name' => 'get-lan-roles', 'can_be_per_lan' => true],
    ['name' => 'get-lan-role-permissions', 'can_be_per_lan' => true],
    ['name' => 'assign-lan-role', 'can_be_per_lan' => true],
    ['name' => 'get-lan-user-roles', 'can_be_per_lan' => true],

    // Global roles
    ['name' => 'create-global-role', 'can_be_per_lan' => false],
    ['name' => 'update-global-role', 'can_be_per_lan' => false],
    ['name' => 'add-permissions-global-role', 'can_be_per_lan' => false],
    ['name' => 'delete-permissions-global-role', 'can_be_per_lan' => false],
    ['name' => 'delete-global-role', 'can_be_per_lan' => false],
    ['name' => 'assign-global-role', 'can_be_per_lan' => false],
    ['name' => 'get-global-roles', 'can_be_per_lan' => false],
    ['name' => 'get-global-role-permissions', 'can_be_per_lan' => false],
    ['name' => 'get-global-user-roles', 'can_be_per_lan' => false],

    // Permissions
    ['name' => 'get-permissions', 'can_be_per_lan' => true],

    // User
    ['name' => 'get-admin-roles', 'can_be_per_lan' => true],
    ['name' => 'get-users', 'can_be_per_lan' => true],
    ['name' => 'get-user-details', 'can_be_per_lan' => true]
];