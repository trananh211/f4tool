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

Route::match(['get', 'post'], '/admin','AdminController@login');

Route::group(['middleware' => ['auth']], function() {
   Route::get('/admin/dashboard','AdminController@dashboard');
   Route::get('/admin/setting','AdminController@setting');
   Route::get('/admin/check-pwd', 'AdminController@checkPass');
   Route::match(['get', 'post'], '/admin/update-pwd', 'AdminController@updatePass');

   //shopify spy
   Route::get('/admin/shopify', 'AdminController@getSpyShopify');
   Route::get('/admin/shopify-give-content/{domain}/{page}','AdminController@shopifyGiveContent');

   //woocommerce api
    Route::get('/woo/woocommerce', 'WooController@getIndex');
    Route::get('/admin/add-new-woo-store','WooController@newStore');
    Route::match(['get', 'post'], '/woo/form-add-new-store', 'WooController@addNewStore');

    Route::get('/woo/dashboard-store/{store_id}','WooController@getDashboardStore');
    Route::get('/woo/order','WooController@getOderStore');
//    excel upload + tracking number + production
    Route::get('/woo/production','WooController@getProduction');
    Route::post('/woo/excel-upload', 'WooController@excelUploadPost')->name('excel.upload.post');

    Route::get('/woo/test-function','WooController@testFunction');


});

Route::get('/woo/test', function() {
    $crawler = Goutte::request('GET', 'https://duckduckgo.com/html/?q=Laravel');
    $crawler->filter('.result__title .result__a')->each(function ($node) {
        dump($node->text());
    });
    return view('welcome');
});

Route::get('auth/register','AdminController@register');
Route::get('logout','AdminController@logout');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
