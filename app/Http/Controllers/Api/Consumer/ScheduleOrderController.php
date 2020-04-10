<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\ScheduleOrder;
use DB;
use App\Http\Resources\Subscription\Order as SubscriptionResource;
class ScheduleOrderController extends Controller
{

    public function index(Request $request)
    {
        try{
            $orders = ScheduleOrder::where('consumer_id', $request->user()->id)->latest()->get();
            $orders = SubscriptionResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['orders' => $orders ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $order = new ScheduleOrder();
            $order->consumer_id = $request->user()->id;
            $order->lifter_id = $request->lifter;
            $order->service_id = $request->service;
            $order->qty = $request->qty;
            $order->shift = $request->shift;
            $order->delivery_time = \App\Helpers\TimeHelper::parseTime($request->prefer_time);
            $order->address = $request->address;
            $order->latitude = $request->latitude;
            $order->longitude = $request->longitude;
            $order->subscribe_type = $request->subscribe_type;
            if($request->subscribe_type == 'daily'){
                $order->days = [];
            }else if($request->subscribe_type == 'weekdays'){
                $order->days = json_decode($request->weekdays);
            }else if($request->subscribe_type == 'monthly'){
                $order->days = json_decode($request->month_days);
            }
            $order->status = 1;
            $order->save();
            DB::commit();
            return response()->json(['status'=>true, 'data' => 'Order susbscribe successfullly' ], 200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['status'=>false, 'error' => "Internal Server Error" ], 405);
        }
    }

    public function updateorder(Request $request)
    {
        return response()->json(['status'=>true, 'data' => $request->all() ], 200);
    }
}
