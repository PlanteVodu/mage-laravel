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

Route::post('/references', 'ReferencesController@store');
Route::patch('/references/{reference}', 'ReferencesController@update');
