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


Route::group(['middleware' => 'cors'], function() {
  Route::post('/admin/login', 'Auth\AdminLoginController@login');
  Route::post('/login', 'Auth\UserLoginController@login');
  Route::post('/register', 'Auth\UserRegisterController@register');
  Route::get('/user-details', 'Auth\UserLoginController@details');
  Route::get('/admin-details', 'Auth\AdminLoginController@details');
  Route::post('/create/cake', 'CakesController@create');
  Route::get('/cake/{id}', 'CakesController@show');
  Route::get('/cakes/order/{order}', 'CakesController@productsPage');
  Route::get('/cakes/category/{category}', 'CakesController@showByCategory');
  Route::post('/create/accessory/', 'AccessoriesController@create');
  Route::get('/accessories', 'AccessoriesController@showAll');
  Route::get('/accessories/{id}', 'AccessoriesController@show');
  Route::post('/submit/order', 'OrdersController@create');
});
