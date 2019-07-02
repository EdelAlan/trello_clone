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

Route::post('login', 'ApiController@login');
Route::post('register', 'ApiController@register');

Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('logout', 'ApiController@logout');

    Route::get('user', 'ApiController@getAuthUser');

    Route::get('columns', 'ColumnController@index');
    Route::post('columns', 'ColumnController@addColumn');
    Route::put('columns/{id}', 'ColumnController@updateColumn');
    Route::delete('columns/{id}', 'ColumnController@deleteColumn');

    Route::get('cards', 'CardController@index');
    Route::post('cards', 'CardController@addCard');
    Route::put('cards/{id}', 'CardController@updateCard');
    Route::delete('cards/{id}', 'CardController@deleteCard');
});
