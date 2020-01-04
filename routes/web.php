<?php

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

Route::post('/actors', 'ActorsController@store');
Route::patch('/actors/{actor}', 'ActorsController@update');

Route::resource('references', 'ReferenceController');

Route::post('kinships', 'KinshipsController@store');
Route::patch('/kinships/{kinship}', 'KinshipsController@update');
