<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\Order;
use App\OpenOrder;
use Carbon\Carbon;
use Validator;

use App\Helpers\AndroidNotifications;

class OrderController extends Controller
{
    
    public function openOrderCreate(Request $request)
    {
        DB::beginTransaction();
        try{

            $openOrder = OpenOrder::find($request->orderid);
            if($openOrder == null){
                return response()->json(['status'=>false, 'data' => false ], 200);
            }
            $order = new Order();
            $order->lifter_id = $request->user()->id;
            $order->consumer_id = $openOrder->consumer_id;
            $order->service_id = $openOrder->service_id;
            $order->qty = $openOrder->qty;
            $order->price = $openOrder->price;
            $order->delivery_time = $openOrder->delivery_time;
            $order->address = $openOrder->address;
            $order->longitude = $openOrder->longitude;
            $order->latitude = $openOrder->latitude;
            $order->type = $openOrder->type;
            $order->status = 'accepted';
            $order->accepted_time = Carbon::now()->toDateTimeString();
            $order->save();
            $openOrder->delete();
            DB::commit();
            // Notifications
            $message = "Order of {$order->service->s_name} for {$order->qty} is accepted.";
            $data = ['order_id' => $order->id, 'type' => 'order'];
            AndroidNotifications::toConsumer("Order Accepted", $message, $order->consumer->pushToken, $data);
            return response()->json(['status'=>true, 'data' => "Order Accepted"], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }


    public function scheduleOrderCreate(Request $request)
    {
        DB::beginTransaction();
        try{
            $openOrder = OpenOrder::find($request->orderid);
            if($openOrder == null){
                return response()->json(['status'=>false, 'data' => false ], 200);
            }
            $order = new Order();
            $order->lifter_id = $request->user()->id;
            $order->consumer_id = $openOrder->consumer_id;
            $order->service_id = $openOrder->service_id;
            $order->qty = $openOrder->qty;
            $order->price = $openOrder->price;
            $order->delivery_time = $openOrder->delivery_time;
            $order->address = $openOrder->address;
            $order->longitude = $openOrder->longitude;
            $order->latitude = $openOrder->latitude;
            $order->type = $openOrder->type;
            $order->status = 'accepted';
            $order->accepted_time = Carbon::now()->toDateTimeString();
            $order->save();
            $openOrder->delete();
            DB::commit();
            // Notifications
            $message = "Order of {$order->service->s_name} for {$order->qty} is accepted.";
            $data = ['order_id' => $order->id, 'type' => 'order'];
            AndroidNotifications::toConsumer("Order Accepted", $message, $order->consumer->pushToken, $data);
            return response()->json(['status'=>true, 'data' => "Order Accepted"], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function show($orderid)
    {
        try{
            $order = OpenOrder::with(['consumer','service'])->where('id',$orderid)->first();
            if($order == null){
                return response()->json(['status'=>false, 'data' => ['Order assigned to anotherone']], 200);
            }
            return response()->json(['status'=>true, 'data' => $order], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try{
            $order = Order::findOrFail($request->orderid);
            $dateTime = Carbon::now()->toDateTimeString();
            $status = strtolower($request->status);
            if($status == 'shipped'){
                $order->status = 'shipped';
                $order->shipped_time = $dateTime;
            }else if($status == 'delivered'){
                $order->status = 'delivered';
                $order->delivered_time = $dateTime;
            }else if($status == 'delivered'){
                $order->status = 'delivered';
                $order->delivered_time = $dateTime;
            }else if($status == 'confirmed'){
                $order->status = 'confirmed';
                $order->confirmed_time = $dateTime;
            }
            $order->save();
            DB::commit();
            $message = "Your order of {$order->service->s_name} is {$status}.";
            $data = ['order_id' => $order->id, 'type' => 'order'];
            AndroidNotifications::toConsumer("Order Accepted", $message, $order->consumer->pushToken, $data);
            return response()->json(['status'=>true, 'data' => "Order Accepted"], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
