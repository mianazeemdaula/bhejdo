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

    Route::get('online', function(){
        $lifters = \App\LifterLocation::where("onwork","1")->get();
        return view('pages.admin.lifters.index', compact('lifters'));
    });
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

Route::get('created_order', function(){
    $order = \App\Order::find(52);
    $sOrder = new \App\ScheduleOrder();
    $sOrder->consumer_id = $order->consumer_id;
    $sOrder->lifter_id = $order->consumer_id;
    $sOrder->service_id = $order->consumer_id;
    $sOrder->qty = 2;
    $sOrder->price = $order->service->s_price;
    $sOrder->charges = $order->service->min_qty_charges;
    $sOrder->delivery_time = $order->delivery_time;
    $sOrder->address = $order->address;
    $sOrder->longitude = $order->longitude;
    $sOrder->latitude = $order->latitude;
    $sOrder->shift = 0;
    $sOrder->note = $order->note;
    $sOrder->schedule_type = 1;
    $sOrder->status = 1;
    $sOrder->save();
    return $sOrder;
});
