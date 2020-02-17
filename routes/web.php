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
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Storage;

Route::get('/image/{url}', function ($url) {
    //return view('welcome');
    // Goto Hell
    return asset("storage/$url");
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/seed', 'SeedController@Seed');
Route::get('/pusher/{message}', 'SeedController@pusher');
Route::get('/indexer/create', 'ElasticController@mapLocation');
Route::get('/indexer/clear', 'ElasticController@clearIndices');

Route::get('/service', 'ServiceController@setservice');