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
use Grimzy\LaravelMysqlSpatial\Types\Point;
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
    Route::resource('subscription', 'SubscriptionController');

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
    Route::resource('sms', 'SmsController');


    

    
    Route::prefix('company')->group(function () {
        Route::resource('product', 'Company\ProductController',['as' => 'company']);
    });

    Route::prefix('admin')->group(function () {
        Route::resource('product', 'admin\ProductController',['as' => 'admin']);
        Route::resource('category', 'admin\CategoryController',['as' => 'admin']);
        Route::resource('order', 'admin\CartOrderController',['as' => 'admin']);
    });






    Route::get('online', function(){
        $lifters = \App\LifterLocation::where("onwork","1")->get();
        return view('pages.admin.lifters.index', compact('lifters'));
    });
});

// Order Proccess url
Route::get('/seed', 'SeedController@Seed');

Route::get('/pages/terms', function(){
    return view('posts/terms_and_policies');
});


Route::get('catProduct', function(){
    $city = 1;
    $cat = \App\Category::with(['products', function($q) use($city){
        $q->where('city_id', $city);
    }])->get();
    return $cat;
});
