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

Route::group(['middleware' => 'admin'], function () {
	Route::get('/listuser', 'UserController@FUNC_LIST')->name('user');
	Route::get('/adduser', 'UserController@FUNC_ADD');
	Route::post('/saveuser', 'UserController@FUNC_SAVE');
	Route::get('/edituser/{id}', 'UserController@FUNC_EDIT');
	Route::post('/updateuser/{id}', 'UserController@FUNC_UPDATE');
	Route::get('/deleteuser/{id}', 'UserController@FUNC_DELETE');
	Route::post('/searchuser', 'UserController@FUNC_SEARCH');

	Route::any('/obat', 'Master\ObatController@index')->name('obat');
	Route::post('/obat/search', 'Master\ObatController@search');
	Route::post('/obat/import', 'Master\ObatController@import');
	Route::post('/obat/remote', 'Master\ObatController@remote');
	Route::get('/obat/delete', 'Master\ObatController@delete')->name('obat-delete');

	Route::any('/kategori', 'Master\KategoriController@index')->name('kategori');
	Route::post('/kategori/search', 'Master\KategoriController@search');
	Route::get('/kategori/delete', 'Master\KategoriController@delete')->name('kategori-delete');

	Route::any('/supplier', 'Master\SupplierController@index')->name('supplier');
	Route::post('/supplier/search', 'Master\SupplierController@search');
	Route::get('/supplier/delete', 'Master\SupplierController@delete')->name('supplier-delete');

	Route::any('/customer', 'Master\CustomerController@index')->name('customer');
	Route::post('/customer/search', 'Master\CustomerController@search');
	Route::get('/customer/delete', 'Master\CustomerController@delete')->name('customer-delete');

	Route::any('/dokter', 'Master\DokterController@index')->name('dokter');
	Route::post('/dokter/search', 'Master\DokterController@search');
	Route::get('/dokter/delete', 'Master\DokterController@delete')->name('dokter-delete');

	Route::any('/setting-biaya', 'Transaksi\SettingBiayaController@index')->name('setting-biaya');
	Route::post('/setting-biaya/search', 'Transaksi\SettingBiayaController@search');
	Route::get('/setting-biaya/delete', 'Transaksi\SettingBiayaController@delete')->name('setting-biaya-delete');
});

Route::any('/penjualan-reguler', 'Transaksi\PenjualanController@index')->name('penjualan-reguler');
Route::any('/penjualan-reguler/print', 'Transaksi\PenjualanController@print');
Route::post('/penjualan-reguler/remote', 'Transaksi\PenjualanController@remote');

Route::any('/penjualan-reguler/{resep}', 'Transaksi\PenjualanController@index')->name('penjualan-resep');
Route::post('/penjualan-reguler/{resep}/remote', 'Transaksi\PenjualanController@remote');

Route::any('/pembelian-reguler', 'Transaksi\PembelianController@index')->name('pembelian-reguler');
Route::any('/pembelian-reguler/print', 'Transaksi\PembelianController@print');
Route::post('/pembelian-reguler/remote', 'Transaksi\PembelianController@remote');

Route::any('/pembelian-reguler/{po}', 'Transaksi\PembelianController@index')->name('pembelian-po');
Route::post('/pembelian-reguler/{po}/remote', 'Transaksi\PembelianController@remote');

Route::any('/pembelian-reguler/po/dashboard', 'Transaksi\PembelianPOController@index')->name('dashboard-po');
Route::any('/pembelian-reguler/po/dashboard/delete', 'Transaksi\PembelianPOController@delete')->name('dashboard-po-delete');
Route::any('/pembelian-reguler/po/dashboard/remote', 'Transaksi\PembelianPOController@remote');

Route::any('/report-penjualan', 'Report\PenjualanController@index')->name('report-penjualan');
Route::any('/report-penjualan/search', 'Report\PenjualanController@search');

Route::any('/report-pembelian', 'Report\PembelianController@index')->name('report-pembelian');
Route::any('/report-pembelian/search', 'Report\PembelianController@search');

Route::any('/retur-penjualan', 'Retur\PenjualanController@index')->name('retur-penjualan');
Route::any('/retur-penjualan/search', 'Retur\PenjualanController@search');