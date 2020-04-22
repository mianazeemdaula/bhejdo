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

//use Illuminate\Support\Facades\Redis;

Route::get('/redis', function () {
    //$app = PRedis::connection();
    //$app->set('user', App\User::with('services')->get()->toJson());
    // $redis = PRedis::command('GEOADD',['locations' , 30.675738, 73.668141, 'lifter-1']);
    // $redis = PRedis::command('GEOADD',['locations' , 30.692198, 73.639817, 'lifter-2']);
    // $redis = PRedis::command('GEOADD',['locations' , 30.679687, 73.654408, 'lifter-3']);
    // //return $app->get('user');
    // $lifters= PRedis::command('GEORADIUS',['partner_locations' ,30.69529,73.660845, 5, 'km', ['WITHDIST','WITHCOORD', 1, 'ASC']]);
    // return $lifters;
    //PRedis::command('DEL',['notification-order-5']);
    PRedis::command('RPUSH',['notification-order-5', "6-Azeem Dhodhi"]);
    PRedis::command('RPUSH',['notification-order-5', "7-Numan Dhodhi"]);
    PRedis::command('RPUSH',['notification-order-5', "6-Azeem Dhodhi"]);
    PRedis::command('RPUSH',['notification-order-5', "8-Hamzam Dhodhi"]);
    return PRedis::lrange('notification-order-5', 0, -1);
});

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
Route::get('/seedLocations', 'SeedController@seedLocations');
Route::get('/getLocation', 'SeedController@getLocation');
Route::get('/sendSMS', 'SeedController@sendSMS');


Auth::routes(['register' => false]);


Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    
    Route::get('/user/approved/{id}', 'UserController@approved');
    Route::resource('service', 'ServiceController');
    Route::resource('wallet', 'WalletController');
    Route::resource('order', 'OrderController');
    Route::resource('subscription', 'ScheduleOrderController');

    Route::get('order/transfer/{id}','OrderController@getTransfer');
    Route::post('order/transfer/{id}','OrderController@postTransfer');
    Route::get('order/livepartners/{id}','OrderController@livePartners');
    Route::get('order/manualSampleConfirm/{id}','OrderController@manualSampleConfirm');
    
    Route::resource('role', 'RoleController');
    Route::resource('permission','PermissionController');
    Route::resource('user', 'UserController');
    Route::get('user/type/consumer', 'UserController@consumer');
    Route::get('user/type/lifter', 'UserController@lifter');
    Route::get('user/type/store', 'UserController@stores');
    Route::get('user/notification/{id}','UserController@notification');
    Route::resource('user.charges', 'ServiceChargeController');
    Route::resource('user.bonus', 'BonusController');
    Route::resource('user.wallet', 'WalletController');
    Route::resource('notification', 'NotificationController');


    

    Route::prefix('store')->group(function () {
        Route::resource('product', 'Store\ProductController');
    });






    Route::get('online', function(){
        $lifters = \App\LifterLocation::where("onwork","1")->get();
        return view('pages.admin.lifters.index', compact('lifters'));
    });
});

// Order Proccess url
Route::get('/seed', 'SeedController@Seed');
Route::get('/pusher/{message}', 'SeedController@pusher');

Route::get('/pages/terms', function(){
    return view('posts/terms_and_policies');
});


// Route::get('deduct', function(){
//     $user = \App\User::find(19);
//     return \App\ServiceCharge::deduct($user->id,"Adjustment of order #88,96,99,101 & 111", "order", 370);
// });

// Route::get('usercreate', function(){
//     $user = new \App\User();
//     $user->name = 'Zaheer Watto';
//     $user->mobile = '03003333444';
//     $user->email = 'arazeem@gmail.com';
//     $user->password = bcrypt('azeem101');
//     $user->address = 'Lahore, Pakistan';
//     $user->account_type = 'support';
//     $user->save();
//     $user->assignRole('support');
// });

Route::get('geo/{lat}/{lng}/{dist}', function($lat, $lng, $dist){
    // $lifters = \App\LifterLocation::raw(['location', '$geoNear', [
    //     'near' => array(
    //         'type' => "Point",
    //         'coordinates' => array(doubleval($lng), doubleval($lat))
    //     ),
    //     'maxDistance' => intval($dist * 1000),
    //     'distanceField' => 'distance'
    // ]]);
    // dd($lifters);
    $lifters = \App\LifterLocation::raw(function($collection) use($lat, $lng, $dist) {
        return $collection->find([
            'location' => [
                '$geoNear' => [
                    '$geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            floatval($lat), // longitude
                            floatval($lng), // latitude
                        ],
                    ],
                    '$maxDistance' => intval($dist * 1000),
                ]
            ]
        ]);
    });
    return $lifters;
    // $mongodb = \DB::connection('mongodb')->getMongoDB();
    // $r = $mongodb->command(
    //     array( 'geoNear' => "lifter_locations",
    //         'near' => array(
    //             'type' => "Point",
    //             'coordinates' => array(doubleval($lng), doubleval($lat))
    //         ),
    //         'spherical' => true,
    //         'maxDistance' => (int) ($dist * 100),
    //         )
    //     );
    // ->where('services','all',[1])->get();
    print_r($r);
});

// Route::get('created_order', function(){
//     $orders = \App\Order::where('created_at', '<', \Carbon\Carbon::now()->subSeconds(60)->toDateTimeString())
//         ->where('lifter_id',2)->where('status','created')->get();
//         return $orders;
// });

// Route::get('changeRole', function(){
//     $user = \App\User::find(22);
//     $user->account_type = 'lifter';
//     $user->removeRole('store');
//     $user->assignRole('lifter');
//     $user->save();

//     $user = \App\User::find(24);
//     $user->account_type = 'lifter';
//     $user->removeRole('store');
//     $user->assignRole('lifter');
//     $user->save();
// });

Route::get('created_order', function(){

    \DB::beginTransaction();
    $sorder = \App\Order::find(245);
    $order = new \App\ScheduleOrder();
    $order->consumer_id = $sorder->consumer_id;
    $order->lifter_id = $sorder->lifter_id;
    $order->service_id = $sorder->service_id;
    $order->qty = 2;
    $order->shift = 2;
    $order->delivery_time = "18:00:00";
    $order->address = $sorder->address;
    $order->latitude = $sorder->latitude;
    $order->longitude = $sorder->longitude;
    $order->subscribe_type = 'daily';
    $order->days = [];
    $order->status = 1;
    $order->save();
    \DB::commit();
    return $order;
});

Route::get('created_schedule_order', function(){

    $sOrder = \App\ScheduleOrder::find(1);
    $order = new \App\Order();
    $order->consumer_id = $sOrder->consumer_id;
    $order->lifter_id = $sOrder->lifter_id;
    $order->service_id = $sOrder->service_id;
    $order->qty = $sOrder->qty;
    $order->price = $sOrder->price;
    $order->note = "";
    $order->address = $sOrder->address;
    $order->longitude = $sOrder->longitude;
    $order->latitude = $sOrder->latitude;
    $order->charges = $sOrder->charges;
    $order->delivery_time = $sOrder->delivery_time;
    $order->save();
    return $order;
});


Route::get('whereTime', function(){
    date_default_timezone_set('Asia/Karachi');
    $date = \Carbon\Carbon::now();
    $nextHour = $date->hour + 1;
    $t1 = "$nextHour:00:00";
    $t2 = "$nextHour:59:00";
    return $date->dayOfWeek ;
    return \App\ScheduleOrder::whereJsonContains('days',$date->day)->get();
    return \App\ScheduleOrder::whereBetween('delivery_time', [$t1, $t2])->get();
});

Route::get('event/{id}', function($id){

    $order = \App\Order::find($id);
    $orderResource = new \App\Http\Resources\Order\Order($order);
    return event(new \App\Events\OrderProcessEvent($order->id, $orderResource));
});