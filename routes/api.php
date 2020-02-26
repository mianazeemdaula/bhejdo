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
    Route::post('login', 'Api\ApiAuthController@login');
    Route::post('register', 'Api\ApiAuthController@registerConsumer');

    Route::group(['middleware' => ['auth:api']], function(){
        // User Info (Profile)
        Route::post('/updatePushToken', 'Api\ApiAuthController@updatePushToken');

        // Lifters
        Route::post('/milk-lifters-near', 'Api\MilkLifterController@getNearMe');

        // Open Orders
        Route::post('/place-order', 'Api\Consumer\OpenOrderController@create');

        
        Route::post('/milk-lifters', 'Api\MilkLifterController@getMilkLifters');
        Route::get('/pending-milk-orders', 'Api\MilkOrderController@pendingMilkOrders'); 
        Route::post('/milk-orders', 'Api\MilkOrderController@milkOrders'); 
        Route::post('/confirm-milk-order', 'Api\MilkOrderController@confirmOrder'); 
    });
});


Route::prefix('lifter')->group(function () {
    Route::post('phone-register', 'Api\ApiAuthController@phoneRegister');
    Route::post('login', 'Api\ApiAuthController@loginLifter');
    Route::post('register', 'Api\ApiAuthController@registerLifter');
    
    
    Route::group(['middleware' => ['auth:api']], function(){
        
        // User Infor (Profile)
        Route::post('/updatePushToken', 'Api\ApiAuthController@updateLifterPushToken');
        Route::get('/account-status', 'Api\ApiAuthController@accountStatus');
        Route::post('/nic-verification', 'Api\ApiAuthController@nicVerificaiton');

        /*
            Orders 
        */
        // Accept Open Order
        Route::post('/open-order-accept', 'Api\Lifter\OrderController@openOrderCreate');
        Route::post('/schedule-order-accept', 'Api\Lifter\OrderController@scheduleOrderCreate');
        // Get Order
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

