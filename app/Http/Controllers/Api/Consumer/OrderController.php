<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Illuminate
use DB;

// Models
use App\Order;
use App\OpenOrder;
use App\ScheduleOrder;
use App\Wallet;

use App\Http\Resources\Milk\Order as OrderResource;
use App\Helpers\AndroidNotifications;

class OrderController extends Controller
{
    public function open(Request $request)
    {
        try{
            $orders = OpenOrder::where('consumer_id', $request->user()->id)->get();
            $orders = OrderResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['orders' => $orders ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function schedule(Request $request)
    {
        try{
            $orders = ScheduleOrder::where('consumer_id', $request->user()->id)->get();
            $orders = OrderResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['orders' => $orders ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function inprocess(Request $request)
    {
        try{
            $orders = Order::where('consumer_id', $request->user()->id)
            ->where('status', '!=' ,'confirmed')->get();
            $orders = OrderResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['orders' => $orders ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function all(Request $request)
    {
        try{
            $orders = Order::where('consumer_id', $request->user()->id)->get();
            $orders = OrderResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['orders' => $orders ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function getOrder(Request $request, $orderid)
    {
        try{
            $order = Order::find($orderid);
            $order =  new OrderResource($order);
            return response()->json(['status'=>true, 'data' => ['order' => $order ]], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function confirmOrder(Request $request)
    {
        DB::beginTransaction();
        try{
            $order = Order::findOrFail($request->orderid);
            $dateTime = Carbon::now()->toDateTimeString();
            $status = strtolower($request->status);
            if($status == 'canceled'){
                $order->status = 'canceled';
                $order->cancel_desc = $request->cancelDescription;
                $order->canceled_time = $dateTime;
            }else if($status == 'confirmed'){
                $order->status = 'confirmed';
                $order->payment = $request->paymentType;
                $order->confirmed_time = $dateTime;
                $lastTrans = Wallet::where('user_id', $request->lifter->id)->latest('id')->first();
                $amount = 0;
                if($lastTrans != null){
                    $amount = $lastTrans->amount;
                }
                $debit = $order->service->s_charges * $order->qty;
                $wallet = new Wallet();
                $wallet->user_id = $order->lifter->id;
                $wallet->description = "Fee of order #{$order->id}";
                $wallet->type = "order";
                $wallet->amount = $amount - $debit;
                $wallet->save();
            }
            $order->save();
            DB::commit();
            $message = "Order for {$order->service->s_name} of {$order->qty} is {$status}.";
            $data = ['order_id' => $order->id, 'type' => 'order'];
            AndroidNotifications::toLifter("Order $status from {$order->consumer->name}", $message, $order->lifter->pushToken, $data);
            return response()->json(['status'=>true, 'data' => "Order {$status}"], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
