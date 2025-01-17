<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function() {
    Route::group(['prefix' => 'client'], function() {
        Route::post('register', 'ClientController@registerClient');
        Route::post('login', 'ClientController@clientLogin');
        Route::put('profile', 'ClientController@addProfile')->middleware('auth:client');
        Route::get('profile', 'ClientController@getClientInfo')->middleware('auth:client');
        Route::get('/users', 'ClientController@getUsersRegisteredUnderCompany')->middleware('auth:client');
        Route::get('/users-at-location', 'ClientController@getUserAtLocationDetails')->middleware('auth:client');
        Route::post('/change-password', 'ClientController@changePassword')->middleware('auth:client');
        Route::get('/user/{id}', 'ClientController@getUserDetails')->middleware('auth:client');
        Route::post('/subscription', 'ClientController@sub_plan')->middleware('auth:client');
        Route::post('/logo', 'ClientController@uploadAvatar')->middleware('auth:client');

    });

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('get-company-profile', 'UserController@getCompanyDetails');
    Route::group(['prefix' => 'user'], function() {
        Route::post('register', 'UserController@registerUser');
        Route::post('login', 'Auth\LoginController@userLogin');
        Route::post('profile', 'UserController@userProfile')->middleware('auth:api');

        Route::post('vehicle', 'UserController@vehicleRegisteration')->middleware('auth:api');
        Route::put('vehicle/{id}', 'UserController@updateVehicle')->middleware('auth:api');
        Route::post('vehicle/add', 'UserController@addPlateNumber')->middleware('auth:api');
        Route::get('vehicles', 'UserController@getPlateNumbers')->middleware('auth:api');
        Route::post('enter/{plate}', 'UserController@ExistingEnterPark')->middleware('auth:api');
        Route::post('exit/{plate}', 'UserController@exitPark')->middleware('auth:api');
        Route::delete('vehicle/{id}', 'UserController@removePlateNumber')->middleware('auth:api');
        Route::post('vehicle/search', 'UserController@searchVehicle')->middleware('auth:api');
       Route::post('logout', 'UserController@userLogout')->middleware('auth:api');
       Route::get('movement', 'UserController@userMovement')->middleware('auth:api');
       Route::post('/change-password', 'UserController@changePassword')->middleware('auth:api');
    });
});
