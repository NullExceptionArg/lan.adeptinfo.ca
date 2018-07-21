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

            $api->get('lan', 'LanController@getLan');
            $api->get('lan/all', 'LanController@getAllLan');

            $api->get('contribution/category', 'ContributionController@getContributionCategories');
            $api->get('contribution', 'ContributionController@getContributions');

        });


        // Authorized requests
        $api->group(['middleware' => ['auth:api']], function ($api) {

            $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {

                $api->post('lan', 'LanController@createLan');
                $api->post('lan/current', 'LanController@setCurrentLan');
                $api->put('lan', 'LanController@updateLan');

                $api->post('contribution/category', 'ContributionController@createContributionCategory');
                $api->delete('contribution/category', 'ContributionController@deleteContributionCategory');
                $api->post('contribution', 'ContributionController@createContribution');
                $api->delete('contribution', 'ContributionController@deleteContribution');

                $api->post('seat/book/{seat_id}', 'SeatController@bookSeat');
                $api->post('seat/confirm/{seat_id}', 'SeatController@confirmArrival');
                $api->delete('seat/confirm/{seat_id}', 'SeatController@unConfirmArrival');
                $api->post('seat/assign/{seat_id}', 'SeatController@assignSeat');

                $api->post('image', 'ImageController@addImage');
                $api->delete('image', 'ImageController@deleteImages');

                $api->delete('user', 'UserController@deleteUser');
                $api->post('user/logout', 'UserController@logOut');
                $api->get('user', 'UserController@getUsers');
                $api->post('user/details', 'UserController@getUserDetails');

            });

        });

    });
});