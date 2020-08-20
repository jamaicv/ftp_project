<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function() {
    Route::group(['prefix' => 'users'], function() {
        Route::match(['get', 'post'], 'create','AdminController@create')->name('create_user');
        Route::match(['get', 'post'], 'mass-create','AdminController@massCreate')->name('mass_create');
    });
});

Route::get('home', 'HomeController@index')->name('home');
Route::match(['get', 'post'], 'update-password', 'UserController@updatePassword')->name('update_password');
Route::match(['get', 'post'], 'load-homework', 'UserController@loadFile')->name('load_homework');
Route::match(['get', 'post'], 'homeworks', 'UserController@getHomeworks')->name('get_homeworks');
Route::match(['get', 'post'], 'download-file/{id}', 'UserController@downloadFile')->name('download_file');