<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {

    $api->group(['middleware' => ['language', 'cors']], function ($api) {

        $api->group(['namespace' => '\Laravel\Passport\Http\Controllers', 'middleware' => ['login']], function ($api) {
            $api->post('oauth/token', 'AccessTokenController@issueToken');
        });

        $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {

            $api->post('user', 'UserController@signUp');
            $api->post('user/facebook', 'UserController@signInFacebook');
            $api->get('user/confirm/{confirmation_code}', 'UserController@confirm');
            $api->post('user/google', 'UserController@signInGoogle');

            $api->get('lan', 'LanController@getLan');
            $api->get('lan/all', 'LanController@getAllLan');

            $api->get('contribution/category', 'ContributionController@getContributionCategories');
            $api->get('contribution', 'ContributionController@getContributions');

            $api->get('tournament/details/{tournament_id}', 'TournamentController@get');
            $api->get('tournament/all', 'TournamentController@getAllTournament');

        });


        // Authorized requests
        $api->group(['middleware' => ['auth:api']], function ($api) {

            $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {

                // User
                $api->post('seat/book/{seat_id}', 'SeatController@bookSeat');
                $api->delete('seat/book/{seat_id}', 'SeatController@unBookSeat');

                $api->post('team', 'TeamController@createTeam');
                $api->post('team/request', 'TeamController@createRequest');
                $api->get('team/request', 'TeamController@getRequests');
                $api->get('team/user', 'TeamController@getUserTeams');
                $api->get('team/details', 'TeamController@getUsersTeamDetails');
                $api->put('team/leader', 'TeamController@changeLeader');
                $api->post('team/accept', 'TeamController@acceptRequest');
                $api->post('team/leave', 'TeamController@leave');
                $api->post('team/kick', 'TeamController@kick');
                $api->delete('team/leader', 'TeamController@deleteLeader');
                $api->delete('team/request/leader', 'TeamController@deleteRequestLeader');
                $api->delete('team/request/player', 'TeamController@deleteRequestPlayer');

                $api->post('tag', 'TagController@createTag');

                $api->delete('user', 'UserController@deleteUser');
                $api->post('user/logout', 'UserController@logOut');
                $api->get('user', 'UserController@getUsers');
                $api->get('user/summary', 'UserController@getUserSummary');
                $api->post('user/details', 'UserController@getUserDetails');

                // Admin
                $api->post('lan', 'LanController@createLan');
                $api->post('lan/current', 'LanController@setCurrentLan');
                $api->put('lan', 'LanController@edit');

                $api->post('contribution/category', 'ContributionController@createContributionCategory');
                $api->delete('contribution/category', 'ContributionController@deleteContributionCategory');
                $api->post('contribution', 'ContributionController@createContribution');
                $api->delete('contribution', 'ContributionController@deleteContribution');

                $api->post('seat/confirm/{seat_id}', 'SeatController@confirmArrival');
                $api->delete('seat/confirm/{seat_id}', 'SeatController@unConfirmArrival');
                $api->post('seat/assign/{seat_id}', 'SeatController@assignSeat');
                $api->delete('seat/assign/{seat_id}', 'SeatController@unAssignSeat');

                $api->post('image', 'ImageController@addImage');
                $api->delete('image', 'ImageController@deleteImages');

                $api->post('tournament', 'TournamentController@createTournament');
                $api->put('tournament/{tournament_id}', 'TournamentController@edit');
                $api->delete('tournament/{tournament_id}', 'TournamentController@delete');
                $api->post('tournament/quit/{tournament_id}', 'TournamentController@quit');

                $api->delete('team/admin', 'TeamController@deleteAdmin');

                $api->post('role/lan', 'RoleController@createLanRole');
                $api->put('role/lan', 'RoleController@editLanRole');
                $api->post('role/lan/assign', 'RoleController@assignLanRole');
                $api->post('role/lan/permissions', 'RoleController@addPermissionsLanRole');
                $api->delete('role/lan/permissions', 'RoleController@deletePermissionsLanRole');
                $api->delete('role/lan', 'RoleController@deleteLanRole');
                $api->get('role/lan', 'RoleController@getLanRoles');
                $api->get('role/lan/permissions', 'RoleController@getLanRolePermissions');
                $api->get('role/lan/users', 'RoleController@getLanUsers');

                $api->post('role/global', 'RoleController@createGlobalRole');
                $api->put('role/global', 'RoleController@editGlobalRole');
                $api->post('role/global/assign', 'RoleController@assignGlobalRole');
                $api->post('role/global/permissions', 'RoleController@addPermissionsGlobalRole');
                $api->delete('role/global/permissions', 'RoleController@deletePermissionsGlobalRole');
                $api->delete('role/global', 'RoleController@deleteGlobalRole');
                $api->get('role/global', 'RoleController@getGlobalRoles');
                $api->get('role/global/permissions', 'RoleController@getGlobalRolePermissions');
                $api->get('role/global/users', 'RoleController@getGlobalUsers');

                $api->get('role/permissions', 'RoleController@getPermissions');

                $api->get('admin/roles', 'UserController@getAdminRoles');
                $api->get('admin/summary', 'UserController@getAdminSummary');
            });

        });

    });
});