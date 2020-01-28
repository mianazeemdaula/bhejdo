<?php

use Illuminate\Http\Request;

/*
| User Registration Routes
*/


Route::prefix('consumer')->group(function () {
    Route::post('login', 'Api\ApiAuthController@login');
    Route::post('register', 'Api\ApiAuthController@registerConsumer');

    Route::group(['middleware' => ['auth:api']], function(){
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/milk-lifters', 'Api\MilkLifterController@getMilkLifters');
        Route::post('/updatePushToken', 'Api\ApiAuthController@updatePushToken');
        Route::post('/place-order', 'Api\MilkOrderController@placeOrder'); 
        Route::get('/pending-milk-orders', 'Api\MilkOrderController@pendingMilkOrders'); 
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
        Route::post('/milk-lifters', 'Api\MilkLifterController@getMilkLifters');
        Route::post('/updatePushToken', 'Api\ApiAuthController@updatePushToken');
        Route::post('/place-order', 'Api\MilkOrderController@placeOrder'); 
        Route::get('/pending-milk-orders', 'Api\MilkOrderController@pendingMilkOrders'); 
        Route::post('/confirm-milk-order', 'Api\MilkOrderController@confirmOrder'); 
    });
});

