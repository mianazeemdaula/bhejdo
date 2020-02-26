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

use Illuminate\Support\Facades\Redis;

Route::get('/redis', function () {
    $app = PRedis::connection();
    $redis = PRedis::command('lrange', ['user', 5, 10]);
    $app->set('user', App\User::with('services')->get()->toJson());
    //return $app->get('user');
    dd($redis);
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/phpinfo', function () {
    return phpinfo();
});

Route::get('/mongo', function(){
   return App\LifterLocation::all(); 
});
Route::get('/mongot', function(){
   return App\LifterLocation::truncate();
});


Auth::routes(['register' => false]);


Route::group(['middleware' => ['auth']], function () {
    
    Route::get('/home', 'HomeController@index')->name('home');

    Route::resource('/user', 'UserController');
    Route::get('/user/approved/{id}', 'UserController@approved');
    Route::resource('/bonus', 'BonusController');
});


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
