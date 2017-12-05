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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', 'UploadController@index');
Route::post('/upload', 'UploadController@uploadSheet');
Route::get('/export', 'ExportController@index');
Route::post('/export', 'ExportController@exportSheet');