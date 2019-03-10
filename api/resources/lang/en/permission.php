<?php

// Le nom des permissions doivent absolument suivre la convention suivante : display-name-nom-de-la-permission
// La description de la description doit absolument suivre la convention suivante : description-nom-de-la-permission
return [
    'display-name-create-lan' => 'Create a new LAN',
    'description-create-lan' => 'Create a new LAN. Careful, this permission should not be given to anyone ... ',
    'display-name-set-current-lan' => 'Set current LAN',
    'description-set-current-lan' => 'Set the LAN that will be shown on the LAN website. Careful, this permission should not be given to anyone ... ',
    'display-name-edit-lan' => 'Edit LAN',
    'description-edit-lan' => 'Edit the name, the starting date, the closing date, the date start, the seat.io keys, the position (Lat, Lng), the number of available places, the price, the rules, and the description of the LAN. Careful, this permission should not be given to anyone ... ',

    'display-name-create-contribution-category' => 'Create contribution category',
    'description-create-contribution-category' => 'Create new contribution categories.',
    'display-name-delete-contribution-category' => 'Delete contribution category',
    'description-delete-contribution-category' => 'Delete contribution categories.',
    'display-name-create-contribution' => 'Create contribution',
    'description-create-contribution' => 'Create contributions.',
    'display-name-delete-contribution' => 'Delete contribution',
    'description-delete-contribution' => 'Delete contributions.',

    'display-name-confirm-arrival' => 'Confirm arrival',
    'description-confirm-arrival' => 'Confirm the arrival of a user who had reserved a seat.',
    'display-name-unconfirm-arrival' => 'Unconfirm arrival',
    'description-unconfirm-arrival' => 'Unconfirm the arrival of a user who had already been set to "Arrived".',

    'display-name-assign-seat' => 'Assign seat',
    'description-assign-seat' => 'Assign a seat to a user',
    'display-name-unassign-seat' => 'Unassign seat',
    'description-unassign-seat' => 'Unassign a seat that had been assigned to a user.',

    'display-name-add-image' => 'Add image',
    'description-add-image' => 'Add an image to that is going to be featured on the main page of the LAN.',
    'display-name-delete-image' => 'Delete image',
    'description-delete-image' => 'Delete an image to that had been added.',

    'display-name-create-tournament' => 'Create tournament',
    'description-create-tournament' => 'Create a new tournament.',
    'display-name-edit-tournament' => 'Edit tournament',
    'description-edit-tournament' => 'Edit the name, the price, the tournament start date, the tournament expected ending date, the amount of players to reach, the amount of teams to reach, and the rules of any tournament.',
    'display-name-add-organizer' => 'Add an organiser',
    'description-add-organizer' => 'Add an organiser to a tournament.',
    'display-name-remove-organizer' => 'Remove an organiser.',
    'description-remove-organizer' => 'Remove an organiser from a tournament',
    'display-name-delete-tournament' => 'Delete tournament',
    'description-delete-tournament' => 'Delete any tournament.',

    'display-name-delete-team' => 'Delete team',
    'description-delete-team' => 'Delete a team.',

    'display-name-create-lan-role' => 'Create a new LAN role',
    'description-create-lan-role' => 'Create a new LAN role.',
    'display-name-update-lan-role' => 'Modify a role of a LAN',
    'description-update-lan-role' => 'Modify a role of a LAN.',
    'display-name-add-permissions-lan-role' => 'Add permissions to a global role',
    'description-add-permissions-lan-role' => 'Add permissions to a global role.',
    'display-name-delete-permissions-lan-role' => 'Remove a permission from a LAN role',
    'description-delete-permissions-lan-role' => 'Remove a permission from a LAN role.',
    'display-name-delete-lan-role' => 'Delete a role of a LAN',
    'description-delete-lan-role' => 'Remove a role from LAN.',
    'display-name-get-lan-roles' => 'Get LAN roles',
    'description-get-lan-roles' => 'Get LAN roles.',
    'display-name-get-lan-role-permissions' => 'Get permissions from a LAN role',
    'description-get-lan-role-permissions' => 'Get permissions from a LAN role.',
    'display-name-assign-lan-role' => 'Assign LAN role',
    'description-assign-lan-role' => 'Assign a LAN role to a user.',
    'display-name-get-lan-user-roles' => 'Get users from a LAN role',
    'description-get-lan-user-roles' => 'Get users from a LAN role.',

    'display-name-create-global-role' => 'Create a new global role',
    'description-create-global-role' => 'Create a new global role.',
    'display-name-update-global-role' => 'Modify a global role',
    'description-update-global-role' => 'Modify a global role.',
    'display-name-add-permissions-global-role' => 'Add permissions to a global role',
    'description-add-permissions-global-role' => 'Add permissions to a global role.',
    'display-name-delete-permissions-global-role' => 'Remove a permission from a global role',
    'description-delete-permissions-global-role' => 'Remove a permission from a global role.',
    'display-name-delete-global-role' => 'Delete a global role',
    'description-delete-global-role' => 'Delete a global role.',
    'display-name-get-global-roles' => 'Get global roles',
    'description-get-global-roles' => 'Get global roles.',
    'display-name-get-global-role-permissions' => 'Get permissions from a global role',
    'description-get-global-role-permissions' => 'Get permissions from a global role.',
    'display-name-assign-global-role' => 'Assign global role',
    'description-assign-global-role' => 'Assign a global role to a user.',
    'display-name-get-global-user-roles' => 'Get users from a global role',
    'description-get-global-user-roles' => 'Get users from a global role.',

    'display-name-get-permissions' => 'Get application permissions',
    'description-get-permissions' => 'Get permissions from the application.',

    'display-name-get-admin-roles' => 'Get the roles of a user',
    'description-get-admin-roles' => 'Get the global and LAN roles of a current user or user',
    'display-name-get-user' => 'Get users',
    'description-get-users' => 'Get the users registered in the API.',
    'display-name-get-user-details' => 'Get user details',
    'description-get-user-details' => 'Get details about a user, for a specific LAN.'
];
