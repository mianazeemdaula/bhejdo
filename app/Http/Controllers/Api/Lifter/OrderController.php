<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use DB;
use App\Order;
use App\OpenOrder;
use App\Bonus;
use App\ServiceCharge;
use Carbon\Carbon;
use Validator;

use App\Http\Resources\Order\Order as OrderResource;
use App\Helpers\AndroidNotifications;

use App\Events\UpdateLifterEvent;

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
            else if($order->status == 'assigned'){
                return response()->json(['status'=>false, 'data'=>"Order already assigned."], 200);
            }else if($order->status == 'canceled'){
                return response()->json(['status'=>false, 'data'=>"Order was canceled by consumer."], 200);
            }else if(Cache::has('order_notificaton_'.$request->user()->id."_".$order->id)){
                return response()->json(['status'=>false, 'data'=>"Order was already cancelled by you."], 200);
            }
            $order->lifter_id = $request->user()->id;
            $order->accepted_time = Carbon::now()->toDateTimeString();
            // Bonus Deduction
            $bonus = Bonus::balance($order->consumer_id);
            $bonusDeducted = 0;
            if($bonus != null && $order->type != 3){
                $deductable = $order->qty * 10;  
                if($bonus->balance >= $deductable){
                    $bonusDeducted = $deductable;
                    $order->bonus = $bonusDeducted;
                    Bonus::deduct($order->consumer_id, "Deduction of order #{$order->id}","order", $bonusDeducted);
                }else if($bonus->balance >= 0){
                    $bonusDeducted = $bonus->balance;
                    $order->bonus = $bonusDeducted;
                    Bonus::deduct($order->consumer_id, "Deduction of order #{$order->id}","order", $bonusDeducted);
                }
            }
            $order->status = 'assigned';
            $order->payable_amount = (($order->qty * $order->price) + $order->charges ) - $bonusDeducted;
            $order->save();
            DB::commit();
            // Notifications to consumer
            $orderResource = new OrderResource($order);
            $message = "Order of {$order->service->s_name} for {$order->qty} is accepted.";
            $data = ['order_id' => $order->id, 'type' => 'order', 'lifter_id' => $order->lifter_id, 'order' => $orderResource];
            AndroidNotifications::toConsumer("Order Accepted", $message, $order->consumer->pushToken, $data);
            return response()->json(['status'=>true, 'data' => "Order Accepted", 'order' => $orderResource ], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function acceptOrder2(Request $request)
    {
        DB::beginTransaction();
        try{
            $order = Order::find($request->orderid);
            if($order == null){
                return response()->json(['status'=>false, 'data' => false ], 200);
            }
            // $order->lifter_id = $request->user()->id;
            // $order->accepted_time = Carbon::now()->toDateTimeString();
            // $order->status = 'assigned';
            // $order->save();
            DB::commit();
            // Notifications to consumer
            $orderResource = new OrderResource($order);
            $message = "Order of {$order->service->s_name} for {$order->qty} is accepted.";
            $data = ['order_id' => $order->id, 'type' => 'order', 'lifter_id' => $order->lifter_id, 'order' => $orderResource];
            AndroidNotifications::toConsumer("Order Accepted", $message, $order->consumer->pushToken, $data);
            return response()->json(['status'=>true, 'data' => "Order Accepted", 'order' => $orderResource ], 200);
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
            if($request->has('notification')){
                $orderid = $request->orderid;
                Cache::put('order_notificaton_'.$request->user()->id."_".$orderid, true, 90000); // 25 hours
                return response()->json(['status'=>true, 'data' => "Notification disable for this order" ], 200);
            }

            $order = Order::findOrFail($request->orderid);
            $dateTime = Carbon::now()->toDateTimeString();
            $status = strtolower($request->status);
            if($status == 'shipped'){
                $order->status = 'shipped';
                $order->shipped_time = $dateTime;
            }else if($status == 'delivered'){
                $order->status = 'delivered';
                $order->delivered_time = $dateTime;
            }else if($status == 'confirmed'){
                $order->status = 'confirmed';
                $order->confirmed_time = $dateTime;
            }else if($status == 'collected'){
                $order->status = 'collected';
                if($order->type == 3){ 
                    $order->collected_amount = 0;
                    $order->payable_amount = 0;
                }else{
                    $debit = ($order->service->s_charges * $order->qty);
                    if($debit > $order->bonus){
                        $debit = $debit - $order->bonus;
                        ServiceCharge::deduct($order->lifter_id,"Service charges of order #{$order->id}", "order", $debit);
                    }else{
                        ServiceCharge::deduct($order->lifter_id,"Service charges of order #{$order->id}", "order", 0);
                    }
                    if($order->charges > 0){
                        ServiceCharge::deduct($order->lifter_id,"Delivery charges of order #{$order->id}", "order", $order->charges / 2);
                    }
                    $order->collected_amount = (($order->qty * $order->price) + $order->charges) - $order->bonus;
                    $order->payable_amount = (($order->qty * $order->price) + $order->charges) - $order->bonus;
                }
            }
            $order->save();
            DB::commit();
            $orderResource = new OrderResource($order);
            $message = "Your order of {$order->service->s_name} is {$status}.";
            $data = ['order_id' => $order->id, 'type' => 'order',  'lifter_id' => $order->lifter_id, 'order' => $orderResource];
            AndroidNotifications::toConsumer("Order status #{$order->id}", $message, $order->consumer->pushToken, $data);
            //event(new UpdateLifterEvent($order->lifter_id, $order));
            return response()->json(['status'=>true, 'data' => "Order Accepted", 'order' => $orderResource ], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function inprocess(Request $request)
    {
        try{
            $orders = Order::where('lifter_id', $request->user()->id)
            ->where('status', '!=' ,'confirmed')->where('status', '!=' ,'collected')->latest()->get();
            $orders = OrderResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['orders' => $orders ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function all(Request $request)
    {
        try{
            //$orders = Order::where('lifter_id', $request->user()->id)->orderByRaw("FIELD(status , 'created', 'assigned', 'shipped') ASC")->get();
            $orders = Order::where('lifter_id', $request->user()->id)->latest()->get();
            $orders = OrderResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['orders' => $orders ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
