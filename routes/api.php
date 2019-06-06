<?php

use Illuminate\Http\Request;

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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/


Route::post('/admin/login', 'Auth\AdminLoginController@login')->name('admin.login.api');
Route::post('/login', 'Auth\UserLoginController@login')->name('user.login.api');
Route::post('/register', 'Auth\UserRegisterController@register')->name('user.register.api');


Route::group(['middleware' => 'auth:api'], function() {
  Route::get('user-details', 'Auth\UserLoginController@details');
  Route::get('admin-details', 'Auth\AdminLoginController@details');
} );
