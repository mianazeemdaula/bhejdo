<?php

use Illuminate\Http\Request;

/*
| User Registration Routes
*/

Route::get('indexer', 'Api\Lifter\EventController@locationIndex');
Route::get('indexer/all', 'Api\Lifter\EventController@getAll');
Route::get('/indexer/doc/{lat}/{lon}/{dis}', 'Api\Lifter\EventController@getLocation');
Route::get('/indexer/status', 'Api\Lifter\EventController@getStatus');

Route::prefix('consumer')->group(function () {
    Route::post('phone-register', 'Api\ApiAuthController@phoneRegisterConsumer');
    Route::post('login', 'Api\Consumer\AuthController@login');
    Route::post('register', 'Api\Consumer\AuthController@register');
    Route::post('forgetpassword', 'Api\Consumer\AuthController@updateForgetPassword');

    Route::group(['middleware' => ['auth:api']], function(){

        
        // User Info (Profile)
        Route::post('/updatePushToken', 'Api\Consumer\AuthController@pushToken');
        Route::prefix('profile')->group(function () {
            Route::get('/', 'Api\Consumer\AuthController@profile');
            Route::post('/update', 'Api\Consumer\AuthController@update');
            Route::post('logout', 'Api\Lifter\AuthController@logout');
        });

        // Lifters
        Route::post('/lifters-near', 'Api\Consumer\LifterController@getNearMe');
        Route::post('/liftersforrepeatorder', 'Api\Consumer\LifterController@getNearLifterForRepeat');

        
        
        Route::prefix('order')->group(function () {
            Route::post('/place-order', 'Api\Consumer\OrderController@create');
            
            Route::get('open', 'Api\Consumer\OrderController@open'); 
            Route::get('inprocess', 'Api\Consumer\OrderController@inprocess'); 
            Route::get('all', 'Api\Consumer\OrderController@all'); 
            Route::get('schedule', 'Api\Consumer\OrderController@schedule'); 
            Route::get('get/{id}', 'Api\Consumer\OrderController@getOrder'); 
            Route::post('update', 'Api\Consumer\OrderController@update'); 
        });

        Route::prefix('bonus')->group(function () {
            Route::resource('/', 'Api\Consumer\BonusController');
            Route::get('/balance', 'Api\Consumer\BonusController@balance');
        });

        Route::prefix('wallet')->group(function () {
            Route::resource('/', 'Api\Consumer\WalletController');
            Route::get('/balance', 'Api\Consumer\WalletController@balance');
        });

        Route::prefix('review')->group(function () {
            Route::resource('/', 'Api\Consumer\ReviewController');
        });

        Route::prefix('review')->group(function () {
            Route::resource('/', 'Api\Consumer\ReviewController');
        });

        Route::prefix('services')->group(function () {
            Route::resource('/', 'Api\Consumer\ServiceController');
            Route::get('/active', 'Api\Consumer\ServiceController@getActive');
        });
        
        
        Route::post('/milk-lifters', 'Api\MilkLifterController@getMilkLifters');
        Route::post('/milk-orders', 'Api\MilkOrderController@milkOrders'); 
        Route::post('/confirm-milk-order', 'Api\MilkOrderController@confirmOrder'); 
    });
});


Route::prefix('lifter')->group(function () {
    Route::post('phone-register', 'Api\ApiAuthController@phoneRegisterLifter');
    Route::post('login', 'Api\Lifter\AuthController@login');
    Route::post('register', 'Api\Lifter\AuthController@register');
    
    Route::post('forgetpassword', 'Api\Lifter\AuthController@updateForgetPassword'); 
    
    Route::group(['middleware' => ['auth:api']], function(){
        
        // User Info (Profile)
        Route::prefix('profile')->group(function () {
            Route::post('logout', 'Api\Lifter\AuthController@logout');
            Route::get('/', 'Api\Lifter\AuthController@profile');
            Route::post('/update', 'Api\Lifter\AuthController@update');
            Route::post('/workinglocation', 'Api\Lifter\AuthController@updateWorkingLocation');
            Route::post('/onwork', 'Api\Lifter\AuthController@onwork');
        });

        Route::get('/account-status', 'Api\Lifter\AuthController@accountStatus');
        Route::post('/updatePushToken', 'Api\Lifter\AuthController@pushToken');
        Route::post('/nic-verification', 'Api\Lifter\AuthController@nicVerificaiton');

        Route::prefix('order')->group(function () {
            Route::get('inprocess', 'Api\Lifter\OrderController@inprocess');
            Route::get('open', 'Api\Lifter\OrderController@open'); 
            Route::get('all', 'Api\Lifter\OrderController@all'); 
            Route::get('schedule', 'Api\Lifter\OrderController@schedule'); 
            Route::post('update', 'Api\Lifter\OrderController@update');
            // New Orders
            Route::get('get/{id}', 'Api\Lifter\OrderController@show');
            Route::post('accept', 'Api\Lifter\OrderController@acceptOrder');
        });

        Route::prefix('bonus')->group(function () {
            Route::resource('/', 'Api\Lifter\BonusController');
            Route::get('/balance', 'Api\Lifter\BonusController@balance');
        });

        Route::prefix('wallet')->group(function () {
            Route::resource('/', 'Api\Lifter\WalletController');
            Route::get('/balance', 'Api\Lifter\WalletController@balance');
            Route::post('/fetchaccount', 'Api\Lifter\WalletController@fetchaccount');
        });

        Route::prefix('servicecharge')->group(function () {
            Route::resource('/', 'Api\Lifter\ServiceChargesController');
            Route::get('/balance', 'Api\Lifter\ServiceChargesController@balance');
        });

        Route::prefix('services')->group(function () {
            Route::resource('/', 'Api\Lifter\ServicesController');
        });

        Route::prefix('review')->group(function () {
            Route::resource('/', 'Api\Lifter\ReviewController');
        });

        // Accept Open Order
        Route::post('/open-order-accept', 'Api\Lifter\OrderController@acceptOrder');
        Route::post('/open-order-accept2', 'Api\Lifter\OrderController@acceptOrder2');
        Route::post('/schedule-order-accept', 'Api\Lifter\OrderController@scheduleOrderCreate');
        // Get Order and Open Order
        Route::get('/get-open-order/{id}', 'Api\Lifter\OrderController@show');
        
        
        // Events
        Route::post('/pushLocation', 'Api\Lifter\EventController@lifterLocation');
    });
});

