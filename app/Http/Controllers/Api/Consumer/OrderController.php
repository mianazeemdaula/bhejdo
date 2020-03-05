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
use App\ServiceCharge;
use App\Bonus;

use App\Http\Resources\Milk\Order as OrderResource;
use App\Helpers\AndroidNotifications;

use Carbon\Carbon;

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

    public function update(Request $request)
    {
        DB::beginTransaction();
        try{
            $order = Order::findOrFail($request->orderid);
            $dateTime = Carbon::now()->toDateTimeString();
            $status = strtolower($request->status);
            $bonusDeducted = 0;
            if($status == 'canceled'){
                $order->status = 'canceled';
                $order->cancel_desc = $request->cancelDescription;
                $order->canceled_time = $dateTime;
            }else if($status == 'confirmed'){
                $order->status = 'confirmed';
                $order->payment_id = $request->paymentType;
                $order->confirmed_time = $dateTime;

                // Logic for bonus deduction
                $bonus = Bonus::balance($request->user()->id);
                if($bonus != null){
                    if($bonus->balance >= 25){
                        $bonusDeducted = 25;
                        Bonus::deduct($request->user()->id, "Deduction of order ${$order->id}","order",25);
                    }else{
                        $bonusDeducted = $bonus->balance;
                        Bonus::deduct($request->user()->id, "Deduction of order ${$order->id}","order",$bonus->balance);
                    }
                }
                $debit = ($order->service->s_charges * $order->qty) - $bonusDeducted;
                ServiceCharge::deduct($order->lifter_id,"Service charges of order #{$order->id}", "order", $debit);
            }
            $order->payable_amount = (($order->qty * $order->price) + $order->charges ) - $bonusDeducted;
            $order->save();
            //
            DB::commit();
            $message = "Order for {$order->service->s_name} of {$order->qty} is {$status}.";
            $data = ['order_id' => $order->id, 'type' => 'order'];
            AndroidNotifications::toLifter("Order $status from {$order->consumer->name}", $message, $order->lifter->pushToken, $data);
            return response()->json(['status'=>true, 'data' => [ "msg" => "Order $status", "amount" => $order->payable_amount ]], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
