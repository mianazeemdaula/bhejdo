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
    Route::post('phone-register', 'Api\ApiAuthController@phoneRegister');
    Route::post('login', 'Api\Consumer\AuthController@login');
    Route::post('register', 'Api\Consumer\AuthController@register');

    Route::group(['middleware' => ['auth:api']], function(){
        // User Info (Profile)
        Route::post('/updatePushToken', 'Api\Consumer\AuthController@pushToken');

        // Lifters
        Route::post('/lifters-near', 'Api\Consumer\LifterController@getNearMe');

        // Open Orders
        Route::post('/place-order', 'Api\Consumer\OpenOrderController@create');
        
        Route::prefix('order')->group(function () {
            Route::get('open', 'Api\Consumer\OrderController@open'); 
            Route::get('inprocess', 'Api\Consumer\OrderController@inprocess'); 
            Route::get('all', 'Api\Consumer\OrderController@all'); 
            Route::get('schedule', 'Api\Consumer\OrderController@schedule'); 
        });
            
        
        Route::post('/milk-lifters', 'Api\MilkLifterController@getMilkLifters');
        Route::post('/milk-orders', 'Api\MilkOrderController@milkOrders'); 
        Route::post('/confirm-milk-order', 'Api\MilkOrderController@confirmOrder'); 
    });
});


Route::prefix('lifter')->group(function () {
    Route::post('phone-register', 'Api\ApiAuthController@phoneRegister');
    Route::post('login', 'Api\Lifter\AuthController@login');
    Route::post('register', 'Api\Lifter\AuthController@register');
    
    
    Route::group(['middleware' => ['auth:api']], function(){
        
        // User Infor (Profile)
        Route::get('/account-status', 'Api\ApiAuthController@accountStatus');
        Route::post('/updatePushToken', 'Api\Lifter\AuthController@pushToken');
        Route::post('/nic-verification', 'Api\Lifter\AuthController@nicVerificaiton');

        /*
            Orders 
        */
        // Accept Open Order
        Route::post('/open-order-accept', 'Api\Lifter\OrderController@openOrderCreate');
        Route::post('/schedule-order-accept', 'Api\Lifter\OrderController@scheduleOrderCreate');
        // Get Order and Open Order
        Route::get('/get-open-order/{id}', 'Api\Lifter\OpenOrderController@show');
        Route::get('/get-order/{id}', 'Api\Lifter\OrderController@show');
        // Update Order
        Route::post('/update-order', 'Api\Lifter\OrderController@update');
        
        Route::get('/inprocess-milk-orders', 'Api\Lifter\MilkOrderController@inProcessOrders'); 
        Route::get('/get-milk-order/{id}', 'Api\Lifter\MilkOrderController@getOrder');

        // Events
        Route::post('/pushLocation', 'Api\Lifter\EventController@lifterLocation');
    });
});

