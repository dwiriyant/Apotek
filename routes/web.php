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

Route::get('/', 'Auth\LoginController@showLoginForm')->name('/');

Route::get('/dashboard', 'HomeController@index')->name('dashboard');

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::post('/image/upload_image', 'ImagesController@upload');
Route::post('/image/list_image', 'ImagesController@modalImage');

Route::get('/listuser', 'UserController@FUNC_LIST')->name('user');
Route::get('/adduser', 'UserController@FUNC_ADD');
Route::post('/saveuser', 'UserController@FUNC_SAVE');
Route::get('/edituser/{id}', 'UserController@FUNC_EDIT');
Route::post('/updateuser/{id}', 'UserController@FUNC_UPDATE');
Route::get('/deleteuser/{id}', 'UserController@FUNC_DELETE');
Route::post('/searchuser', 'UserController@FUNC_SEARCH');

Route::any('/obat', 'Master\ObatController@index')->name('obat');
Route::post('/obat/search', 'Master\ObatController@search');
Route::post('/obat/remote', 'Master\ObatController@remote');
Route::get('/obat/delete', 'Master\ObatController@delete')->name('obat-delete');

Route::any('/kategori', 'Master\KategoriController@index')->name('kategori');
Route::post('/kategori/search', 'Master\KategoriController@search');
Route::post('/kategori/remote', 'Master\KategoriController@remote');
Route::get('/kategori/delete', 'Master\KategoriController@delete')->name('kategori-delete');