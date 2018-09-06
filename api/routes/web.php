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

        $api->group(['prefix' => 'oauth'], function ($api) {
            $api->post('token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
        });

        $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {

            $api->post('user', 'UserController@signUp');
            $api->post('user/facebook', 'UserController@signInFacebook');
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

                // Admin
                $api->post('lan', 'LanController@createLan');
                $api->post('lan/current', 'LanController@setCurrentLan');
                $api->put('lan', 'LanController@updateLan');

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
                $api->put('tournament/{tournament_id}', 'TournamentController@editTournament');
                $api->delete('tournament/{tournament_id}', 'TournamentController@delete');
                $api->post('tournament/quit/{tournament_id}', 'TournamentController@quit');

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


                $api->delete('team/admin', 'TeamController@deleteAdmin');
                $api->delete('team/leader', 'TeamController@deleteLeader');
                $api->delete('team/request/leader', 'TeamController@deleteRequestLeader');
                $api->delete('team/request/player', 'TeamController@deleteRequestPlayer');

                $api->post('tag', 'TagController@createTag');

                $api->delete('user', 'UserController@deleteUser');
                $api->post('user/logout', 'UserController@logOut');
                $api->get('user', 'UserController@getUsers');
                $api->get('user/summary', 'UserController@getUserSummary');
                $api->post('user/details', 'UserController@getUserDetails');

            });

        });

    });
});