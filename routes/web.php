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

// Route::get('/admin','AdminController@login');
Route::match(['get', 'post'], '/admin','AdminController@login');

Route::group(['middleware' => ['auth']], function() {
   Route::get('/admin/dashboard','AdminController@dashboard');
   Route::get('/admin/setting','AdminController@setting');
   Route::get('/admin/check-pwd', 'AdminController@checkPass');
   Route::match(['get', 'post'], '/admin/update-pwd', 'AdminController@updatePass');

   //shopify spy
   Route::get('/admin/shopify', 'AdminController@getSpyShopify');
   Route::get('/admin/shopify-give-content/{domain}/{page}','AdminController@shopifyGiveContent');

});

Route::get('auth/register','AdminController@register');
Route::get('logout','AdminController@logout');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
