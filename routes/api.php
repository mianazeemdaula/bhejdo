<?php

use Illuminate\Http\Request;

/*
| User Registration Routes
*/

Route::get('indexer', 'Api\Lifter\EventController@locationIndex');
Route::get('/indexer/doc/{lat}/{lon}', 'Api\Lifter\EventController@getLocation');
Route::get('/indexer/status', 'Api\Lifter\EventController@getStatus');

Route::prefix('consumer')->group(function () {
    Route::post('phone-register', 'Api\ApiAuthController@phoneRegister');
    Route::post('login', 'Api\ApiAuthController@login');
    Route::post('register', 'Api\ApiAuthController@registerConsumer');

    Route::group(['middleware' => ['auth:api']], function(){
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/milk-lifters', 'Api\MilkLifterController@getMilkLifters');
        Route::post('/milk-lifters-near', 'Api\MilkLifterController@getNearMe');
        Route::post('/updatePushToken', 'Api\ApiAuthController@updatePushToken');
        Route::post('/place-order', 'Api\MilkOrderController@placeOrder'); 
        Route::get('/pending-milk-orders', 'Api\MilkOrderController@pendingMilkOrders'); 
        Route::post('/milk-orders', 'Api\MilkOrderController@milkOrders'); 
        Route::post('/confirm-milk-order', 'Api\MilkOrderController@confirmOrder'); 
    });
});


Route::prefix('lifter')->group(function () {
    Route::post('login', 'Api\ApiAuthController@loginLifter');
    Route::post('register', 'Api\ApiAuthController@registerConsumer');
    
    Route::group(['middleware' => ['auth:api']], function(){
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/updatePushToken', 'Api\ApiAuthController@updatePushToken');
        Route::get('/inprocess-milk-orders', 'Api\Lifter\MilkOrderController@inProcessOrders'); 
        Route::post('/update-milk-order', 'Api\Lifter\MilkOrderController@updateOrder');
        Route::get('/get-milk-order/{order_id}', 'Api\Lifter\MilkOrderController@getOrder');

        // Events
        Route::post('/pushLocation', 'Api\Lifter\EventController@lifterLocation');
    });
});

