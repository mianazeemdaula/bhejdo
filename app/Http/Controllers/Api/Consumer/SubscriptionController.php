<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\Subscription;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        try{
            $orders = Subscription::leftJoin('cart_orders', function($join) use($request)
            {
                $join->on('subscriptions.order_id', '=', 'cart_orders.id')
                ->where('cart_orders.consumer_id',  $request->user()->id);
            })->pluck('cart_orders.id')->toArray();
            $orders = Subscription::whereIn('order_id',$orders)->get();
            $orders = \App\Http\Resources\V2\Consumer\SubscriptionResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['subscriptions' => $orders ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $deliveryTime = strlen($request->deliveryTime) == 2 ? $request->deliveryTime :  "0".$request->deliveryTime;
            $subscriptionType = strtolower(str_replace(' ', '',$request->subscribe_type));
            $subscription = new Subscription();
            $subscription->order_id = $request->order;
            $subscription->shift = $request->shift;
            $subscription->delivery_time = $deliveryTime.":00:00";
            $subscription->subscribe_type = $subscriptionType;
            if($subscriptionType == 'daily'){
                $subscription->days = null;
            }else if($subscriptionType == 'weekdays'){
                $subscription->days = $request->weekDays;
            }else if($subscriptionType == 'monthly'){
                $subscription->days = $request->monthDays;
            }
            $subscription->status = 1;
            $subscription->save();
            DB::commit();
            return response()->json(['status'=>true, 'data' => 'Subscription added successfullly!'], 200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['status'=>false, 'error' => "$e" ], 405);
        }
    }
}
