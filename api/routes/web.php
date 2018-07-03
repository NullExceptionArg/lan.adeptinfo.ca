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

    $api->group(['prefix' => 'oauth'], function ($api) {
        $api->post('token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
    });

    $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {

        $api->post('user', 'UserController@signUp');

        $api->get('lan/{lan_id}', 'LanController@getLan');
        $api->get('lans', 'LanController@getLans');

        $api->get('lan/{lan_id}/contribution-category', 'ContributionController@getContributionCategories');
        $api->get('lan/{lan_id}/contribution', 'ContributionController@getContributions');

    });


    // Authorized requests
    $api->group(['middleware' => ['auth:api', 'cors']], function ($api) {

        $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {

            $api->post('lan', 'LanController@createLan');
            $api->get('lan/{lan_id}/select', 'LanController@selectCurrentLan');
            $api->post('lan/{lan_id}/rules', 'LanController@updateLanRules');

            $api->post('lan/{lan_id}/contribution-category', 'ContributionController@createContributionCategory');
            $api->delete('lan/{lan_id}/contribution-category/{contribution_category_id}', 'ContributionController@deleteContributionCategory');
            $api->post('lan/{lan_id}/contribution', 'ContributionController@createContribution');
            $api->delete('lan/{lan_id}/contribution/{contribution_id}', 'ContributionController@deleteContribution');

            $api->post('lan/{lan_id}/book/{seat_id}', 'SeatController@bookSeat');
            $api->post('lan/{lan_id}/confirm/{seat_id}', 'SeatController@confirmArrival');
            $api->delete('lan/{lan_id}/confirm/{seat_id}', 'SeatController@unConfirmArrival');

            $api->post('lan/{lan_id}/image', 'ImageController@addImage');
            $api->delete('lan/{lan_id}/image/{image_id}', 'ImageController@deleteImages');

            $api->delete('user', 'UserController@deleteUser');
            $api->post('user/logout', 'UserController@logOut');
            $api->get('user', 'UserController@getUsers');

        });

    });


});