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

Route::get('/migrate', 'MigrateController@index');

Route::get('/dashboard', 'HomeController@index')->name('dashboard');
Route::post('/dashboard/ajax', 'HomeController@ajaxRequest');

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::post('/image/upload_image', 'ImagesController@upload');
Route::post('/image/list_image', 'ImagesController@modalImage');

Route::get('/images-bank', 'ImageBank\ImageBankController@index')->name('images-bank');
Route::post('/images-bank/search', 'ImageBank\ImageBankController@search');
Route::any('/images-bank/meta', 'ImageBank\ImageBankController@meta');
Route::post('/images-bank/crop', 'ImageBank\ImageBankController@crop');
Route::post('/images-bank/rotate', 'ImageBank\ImageBankController@rotate');

Route::get('/send_mail', 'EmailController@send')->name('send-email');

Route::get('/listuser', 'UserController@FUNC_LIST')->name('user');
Route::get('/adduser', 'UserController@FUNC_ADD');
Route::post('/saveuser', 'UserController@FUNC_SAVE');
Route::get('/edituser/{id}', 'UserController@FUNC_EDIT');
Route::post('/updateuser/{id}', 'UserController@FUNC_UPDATE');
Route::get('/deleteuser/{id}', 'UserController@FUNC_DELETE');
Route::post('/searchuser', 'UserController@FUNC_SEARCH');

Route::any('/social-media', 'Content\SocialMediaController@index')->name('social-media');
Route::post('/social-media/search', 'Content\SocialMediaController@search');
Route::get('/social-media/active', 'Content\SocialMediaController@active')->name('socmed-active');
Route::get('/social-media/delete', 'Content\SocialMediaController@delete')->name('socmed-delete');
Route::post('/social-media/remote', 'Content\SocialMediaController@remote');

Route::get('/news', 'Content\CommunityController@index')->name('news');
Route::post('/news/search', 'Content\CommunityController@search');
Route::any('/news/edit', 'Content\CommunityController@edit')->name('edit-news');
Route::get('/news/preview', 'Content\CommunityController@preview')->name('preview-news');
Route::post('/news/remote', 'Content\CommunityController@remote');
Route::post('/news/delete', 'Content\CommunityController@delete');

Route::get('/photonews', 'Content\CommunityController@index')->name('photonews');
Route::post('/photonews/search', 'Content\CommunityController@search');
Route::any('/photonews/edit', 'Content\CommunityController@edit')->name('edit-photonews');
Route::get('/photonews/preview', 'Content\CommunityController@preview')->name('preview-photonews');
Route::post('/photonews/remote', 'Content\CommunityController@remote');
Route::post('/photonews/delete', 'Content\CommunityController@delete');

Route::get('/video', 'Content\CommunityController@index')->name('video');
Route::post('/video/search', 'Content\CommunityController@search');
Route::any('/video/edit', 'Content\CommunityController@edit')->name('edit-video');
Route::get('/video/preview', 'Content\CommunityController@preview')->name('preview-video');
Route::post('/video/remote', 'Content\CommunityController@remote');
Route::post('/video/delete', 'Content\CommunityController@delete');

Route::get('/report/member', 'Report\ReportController@index')->name('report-member');
Route::post('/report/member/search', 'Report\ReportController@search');
Route::post('/report/member/remote', 'Report\ReportController@remote');

Route::get('/report/kpi', 'Report\KPIController@index')->name('report-kpi');
Route::post('/report/kpi/search', 'Report\KPIController@search');
Route::post('/report/kpi/remote', 'Report\KPIController@remote');

Route::any('/campaign-schedule', 'Content\CampaignController@index')->name('campaign-schedule');
Route::get('/campaign-schedule/delete', 'Content\CampaignController@delete')->name('campaign-delete');
Route::post('/campaign-schedule/search', 'Content\CampaignController@search');

Route::any('/campaign-banner', 'Content\CampaignBannerController@index')->name('campaign-banner');
Route::get('/campaign-banner/delete', 'Content\CampaignBannerController@delete')->name('campaign-banner-delete');
Route::post('/campaign-banner/search', 'Content\CampaignBannerController@search');