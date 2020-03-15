<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\Order;
use App\OpenOrder;
use Carbon\Carbon;
use Validator;

use App\Http\Resources\Order\Order as OrderResource;
use App\Helpers\AndroidNotifications;

class OrderController extends Controller
{
    
    public function acceptOrder(Request $request)
    {
        DB::beginTransaction();
        try{

            $order = Order::find($request->orderid);
            if($order == null){
                return response()->json(['status'=>false, 'data' => false ], 200);
            }
            $order->lifter_id = $request->user()->id;
            $order->accepted_time = Carbon::now()->toDateTimeString();
            $order->status = 'assigned';
            $order->save();
            DB::commit();
            // Notifications to consumer
            $message = "Order of {$order->service->s_name} for {$order->qty} is accepted.";
            $data = ['order_id' => $order->id, 'type' => 'order', 'lifter_id' => $order->lifter_id];
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
            $order->payable_amount = 0.0;
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
            $order = Order::find($orderid);
            if($order == null){
                return response()->json(['status'=>false, 'data' => ['Order not found']], 200);
            }
            $order =  new OrderResource($order);
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
            $data = ['order_id' => $order->id, 'type' => 'order',  'lifter_id' => $order->lifter_id];
            AndroidNotifications::toConsumer("Order status #{$order->id}", $message, $order->consumer->pushToken, $data);
            return response()->json(['status'=>true, 'data' => "Order Accepted"], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function inprocess(Request $request)
    {
        try{
            $orders = Order::where('lifter_id', $request->user()->id)
            ->where('status', '!=' ,'confirmed')->latest()->get();
            $orders = OrderResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['orders' => $orders ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function all(Request $request)
    {
        try{
            $orders = Order::where('lifter_id', $request->user()->id)->latest()->get();
            $orders = OrderResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['orders' => $orders ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
