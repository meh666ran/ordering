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

  Route::group(['name' => 'authentication'], function() {
      Route::post('/admin/login', 'Auth\AdminLoginController@login');
      Route::post('/register', 'Auth\UserRegisterController@register');
      Route::post('/login', 'Auth\UserLoginController@login');
      Route::get('/admin-details', 'Auth\AdminLoginController@details');
      Route::get('/user-details', 'Auth\UserLoginController@details');
  });

  Route::group(['name' => 'cakes'], function() {
    Route::post('/create/cake', 'CakesController@create');
    Route::get('/cake/{id}', 'CakesController@show');
    Route::get('/cakes/order/{order}', 'CakesController@productsPage');
    Route::get('/cakes/category/{category}', 'CakesController@showByCategory');
    Route::put('/update/cake/{id}', 'CakesController@update');
    Route::delete('/delete/cake/{id}', 'CakesController@destroy');

    Route::post('/update/cake/{id}/image', 'CakesController@updateImage');
  });

  Route::group(['name' => 'accessories'], function() {
    Route::post('/create/accessory/', 'AccessoriesController@create');
    Route::get('/accessories', 'AccessoriesController@showAll');
    Route::get('/accessories/{id}', 'AccessoriesController@show');
    Route::put('/update/accessory/{id}', 'AccessoriesController@update');
    Route::delete('/delete/accessory/{id}', 'AccessoriesController@destroy');
  });

  Route::post('/submit/order', 'OrdersController@create');

});
