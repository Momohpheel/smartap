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
    //Auth::routes();
    Route::group(['prefix' => 'client'], function() {
        Route::post('register', 'ClientController@registerClient');
        Route::get('{id}', 'ClientController@getOneClient');
        Route::get('/', 'ClientController@getAllClients');
    });
    Route::group(['prefix' => 'user'], function() {
        Route::post('register', 'UserController@registerUser');
        Route::post('login', 'Auth\LoginController@userLogin');
        Route::post('plate/{id}/add', 'UserController@addPlateNumber');
    });
});
