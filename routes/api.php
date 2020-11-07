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
        Route::get('/', 'ClientController@getOneClient');
        Route::get('/active', 'ClientController@getAllActiceMembers')->middleware('auth:client');
        Route::get('/{plate_number}', 'ClientController@getUserDetails')->middleware('auth:client');

});

    Route::group(['prefix' => 'user'], function() {
        Route::post('register', 'UserController@registerUser');
        Route::post('login', 'Auth\LoginController@userLogin');
        Route::post('profile', 'UserController@userProfile')->middleware('auth:api');
        Route::post('vehicle', 'UserController@vehicleRegisteration')->middleware('auth:api');
        Route::post('vehicle/add', 'UserController@addPlateNumber')->middleware('auth:api');
        Route::get('vehicles', 'UserController@getPlateNumbers')->middleware('auth:api');
        Route::post('enter/{plate}', 'UserController@ExistingEnterPark')->middleware('auth:api');
        Route::post('exit/{plate}', 'UserController@exitPark')->middleware('auth:api');
        Route::post('vehicle/delete/{plate}', 'UserController@removePlateNumber')->middleware('auth:api');
        Route::get('search/{plate}', 'UserController@searchVehicle')->middleware('auth:api');
       Route::post('logout', 'UserController@userLogout')->middleware('auth:api');
       Route::get('movement', 'UserController@userMovement')->middleware('auth:api');



    });
});
