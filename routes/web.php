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

// use Illuminate\Support\Facades\Redis;

// Route::get('/redis', function () {
//     // $app = PRedis::connection();
//     // $app->set('user', App\User::with('services')->get()->toJson());
//     // $redis = PRedis::command('GEOADD',['locations' , 30.675738, 73.668141, 'lifter-1']);
//     // $redis = PRedis::command('GEOADD',['locations' , 30.692198, 73.639817, 'lifter-2']);
//     // $redis = PRedis::command('GEOADD', ['locations' , 30.679687, 73.654408, 'lifter-3']);
//     // //return $app->get('user');
//     $lifters= PRedis::command('GEORADIUS',['locations' ,30.685629,73.660845, 5, 'km', ['WITHDIST','WITHCOORD', 10, 'ASC']]);
//     return $lifters;
// });

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/phpinfo', function () {
//     return phpinfo();
// });

Route::get('/mongo', function(){
   return App\LifterLocation::all(); 
});
// Route::get('/mongot', function(){
//    return App\LifterLocation::truncate();
// });

Route::get('/dbtest', 'SeedController@test');


Auth::routes(['register' => false]);


Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    
    Route::get('/user/approved/{id}', 'UserController@approved');
    Route::resource('service', 'ServiceController');
    Route::resource('wallet', 'WalletController');
    Route::resource('order', 'OrderController');

    Route::get('order/transfer/{id}','OrderController@getTransfer');
    Route::post('order/transfer/{id}','OrderController@postTransfer');
    
    Route::resource('user', 'UserController');
    Route::get('user/type/consumer', 'UserController@consumer');
    Route::get('user/type/lifter', 'UserController@lifter');
    Route::get('user/type/store', 'UserController@stores');
    Route::resource('user.charges', 'ServiceChargeController');
    Route::resource('user.bonus', 'BonusController');
});


Route::get('/seed', 'SeedController@Seed');
Route::get('/pusher/{message}', 'SeedController@pusher');

Route::get('/pages/terms', function(){
    return view('posts/terms_and_policies');
});


// Route::get('usercreate', function(){
//     $user = new \App\User();
//     $user->name = 'Zaheer Watto';
//     $user->mobile = '03211234567';
//     $user->email = 'mzawattu@gmail.com';
//     $user->password = bcrypt('zaheer101');
//     $user->address = 'Lahore, Pakistan';
//     $user->account_type = 'admin';
//     $user->reffer_id = "ZAHEERWATU";
//     $user->save();
//     $user->assignRole('admin');
// });

Route::get('geo/{lat}/{lng}/{dist}', function($lat, $lng, $dist){
    $lifters = \App\LifterLocation::where('location', 'near', [
        '$geometry' => [
            'type' => 'Point',
            'coordinates' => [
                floatval($lat), // longitude
                floatval($lng), // latitude
            ],
        ],
        '$maxDistance' => intval($dist * 1000)
    ])
    ->where('services','all',[1])->get();
    return $lifters;
});

Route::get('created_order', function(){
    $orders = \App\Order::where('created_at', '<', \Carbon\Carbon::now()->subSeconds(60)->toDateTimeString())
        ->where('lifter_id',2)->where('status','created')->get();
        return $orders;
});


Route::get('send_order', function(){
    $order = \App\Order::find(61);
    $lifter = \App\User::find(6);
    $message = "Place order of $order->qty liter of ".$order->service->s_name.". Please deliver as earliest.";
    $args =  ["type" => 'new_order', 'order_id' => $order->id , 'order' => new \App\Http\Resources\Order\Order($order)];
    $notification = \App\Helpers\AndroidNotifications::toOnlineLifter("New Order", $message, $lifter->pushToken,$args);
    return $notification;
});
Route::get('notifiorders', function(){
    $orders = \App\Order::where('created_at', '<', \Carbon\Carbon::now()->subSeconds(60)->toDateTimeString())
    ->where('lifter_id',2)->where('status','created')->get();
    //$orders = Order::where('lifter_id',2)->get();
    foreach($orders as $order){
        $response = \App\Helpers\OrderProcess::orderCreated($order);
        print_r($response);
    }
});

use Illuminate\Support\Facades\Cache;

Route::get('cache', function(){
    $lifterid = 2;
    $value = false;
    if(Cache::has('neworder_time_'.$lifterid)){
        $value = true;
    }else{
        Cache::put('neworder_time_'.$lifterid, true, 100);
        $value = false;
    }
    return "$value";
});

Route::get('profile/{id}', function($id){
    return \App\User::find($id)->profile;
});

Route::get('changeRole', function(){
    $user = User::find(25);
    $user->removeRole('store');
    $user->assignRole('lifter');

    $user = User::find(23);
    $user->removeRole('consumer');
    $user->assignRole('lifter');
});