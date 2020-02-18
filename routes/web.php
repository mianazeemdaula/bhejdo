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

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/seed', 'SeedController@Seed');
Route::get('/pusher/{message}', 'SeedController@pusher');
Route::get('/indexer/create', 'ElasticController@mapLocation');
Route::get('/indexer/clear', 'ElasticController@clearIndices');

Route::get('/service', 'ServiceController@setservice');

use Illuminate\Support\Str;
Route::get('id/{id}', function($username){
    $refferid = Str::limit($username,5,"");
    $random = Str::random(5);
    $newid = strtoupper("$refferid$random");
    return $refferid."-".$newid;
});
