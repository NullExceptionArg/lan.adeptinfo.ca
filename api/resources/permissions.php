<?php

return [

    /// Route based

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

    // Image
    ['name' => 'add-image', 'can_be_per_lan' => true],
    ['name' => 'delete-image', 'can_be_per_lan' => true],

    // Tournament
    ['name' => 'create-tournament', 'can_be_per_lan' => true],
    ['name' => 'edit-tournament', 'can_be_per_lan' => true],
    ['name' => 'delete-tournament', 'can_be_per_lan' => true],
    ['name' => 'quit-tournament', 'can_be_per_lan' => true],

    // Team
    ['name' => 'delete-team', 'can_be_per_lan' => true],

    // Lan roles
    ['name' => 'create-lan-role', 'can_be_per_lan' => true],
    ['name' => 'edit-lan-role', 'can_be_per_lan' => true],
    ['name' => 'add-permissions-lan-role', 'can_be_per_lan' => true],
    ['name' => 'delete-permissions-lan-role', 'can_be_per_lan' => true],
    ['name' => 'delete-lan-role', 'can_be_per_lan' => true],
    ['name' => 'get-lan-roles', 'can_be_per_lan' => true],
    ['name' => 'get-lan-role-permissions', 'can_be_per_lan' => true],
    ['name' => 'assign-lan-role', 'can_be_per_lan' => true],
    ['name' => 'get-lan-user-roles', 'can_be_per_lan' => true],

    // Global roles
    ['name' => 'create-global-role', 'can_be_per_lan' => false],
    ['name' => 'edit-global-role', 'can_be_per_lan' => false],
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
    ['name' => 'admin-summary', 'can_be_per_lan' => true],
    ['name' => 'get-admin-roles', 'can_be_per_lan' => true]
];