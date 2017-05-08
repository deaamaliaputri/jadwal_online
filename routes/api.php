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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::resource('contacts','ContactController');
Route::resource('teachers','TeachersController');
Route::resource('daftar','DaftarController');
Route::resource('departments','DepartmentsController');
Route::resource('kelas','KelasController');
Route::resource('schedules','SchedulesController');
Route::get('cek-cetak-schedules/{id}/{id2}','SchedulesController@getcekcetak');
Route::resource('students','StudentsController');
Route::resource('subjects','SubjectsController');
Route::get('getList-teachers','TeachersController@getList');
Route::get('getList-departments','DepartmentsController@getList');
Route::get('getList-kelas','KelasController@getList');
Route::get('getList-subjects','SubjectsController@getList');
////auth
Route::group(['namespace' => 'Auth'], function () {

    // Authentication routes...
    Route::get('get-login', 'LoginController@getLogin');
    Route::get('logout', 'LoginController@getLogout');
    Route::get('post-login', 'LoginController@getLogin');
    Route::post('post-login', 'LoginController@postLogin');
});
////auth
Route::group(['namespace' => 'Cetak'], function () {
    // Cetak...
    Route::get('cetak-daftar/{id}/{id2}', 'CetakDaftar@Daftar');
});

Route::get('get-session', 'DaftarController@getSession');