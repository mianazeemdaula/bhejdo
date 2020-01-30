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

Route::get('/', function () {
    //return view('welcome');
    $user = User::find(1);
    echo $user->created_at;
    $to = Carbon::createFromFormat('Y-m-d H:s:i', $user->created_at);
    $from = Carbon::now();
    echo "<br>";
    $diff_in_minutes = $from->diffInMinutes($to);
    print_r($diff_in_minutes); // Output: 20
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/seed', 'SeedController@Seed');
Route::get('/pusher/{message}', 'SeedController@pusher');
