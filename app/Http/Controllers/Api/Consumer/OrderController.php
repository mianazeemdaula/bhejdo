<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Illuminate
use DB;
use Validator;

// Models
use App\Order;
use App\ScheduleOrder;
use App\Wallet;
use App\ServiceCharge;
use App\Bonus;
use App\Service;

use App\Http\Resources\Order\Order as OrderResource;
use App\Helpers\AndroidNotifications;
use App\Helpers\OrderProcess;

use App\Events\UpdateLifterEvent;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        DB::beginTransaction();
        try{
            $validator = Validator::make( $request->all(), [
                'consumer_id' => 'required',
                'qty' => 'required',
                'price' => 'required',
                'address' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'service_id' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }

            if($request->has('test')){
                return response()->json(['status'=>true, 'data' => $request->all()], 200);
            }

            $service = Service::findOrFail($request->service_id);
            
            $order = new Order();
            $order->consumer_id = $request->user()->id;
            $order->lifter_id = 2;
            $order->service_id = $request->service_id;
            $order->qty = $request->qty;
            $order->price = $service->s_price;
            $order->note = $request->note;
            
            $order->address = $request->address;
            $order->longitude = $request->longitude;
            $order->latitude = $request->latitude;
            if($request->has('sample')){
                $order->type = 3;
                $order->charges = 0;
            }
            else if($request->qty < $service->min_qty){
                $order->charges = $service->min_qty_charges;
            }else{
                $order->charges = 0;
            }

            if($request->has('shift')){
                //$order->shift = $service->shift;
            }else{
                $order->delivery_time = $request->delivery_time;
            }
            $order->save();
            $response = OrderProcess::orderCreated($order);
            DB::commit();
            $data = ['msg' => 'Order Placed Successfully', 'response' => $response];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

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
            $orders = Order::where('consumer_id', $request->user()->id)->latest()->get();
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
                $order->cancel_desc = "";
                $balance = Bonus::balance($request->user()->id);
                if($balance->balance > 10)
                    Bonus::deduct($request->user()->id, "Cancel order panality #{$order->id}","order", 10);
            }else if($status == 'confirmed'){
                if($order->confirmed_time != null){
                    return response()->json(['status'=>false, 'data' => [ "msg" => "Already Confirmed", ]], 200);
                }

                if($order->type == 3){ // Sample order
                    $amount = $order->qty * $order->service->lifter_price;
                    ServiceCharge::add($order->lifter_id,"Sample order #{$order->id}", "order",$amount );
                }else{
                    // Logic for bonus d#eduction
                    if($order->payable_amount == 0){
                        $bonus = Bonus::balance($request->user()->id);
                        if($bonus != null){
                            $deductable = $order->qty * 10;  
                            if($bonus->balance >= $deductable){
                                $bonusDeducted = $deductable;
                                $order->bonus_paid = $bonusDeducted;
                                Bonus::deduct($request->user()->id, "Deduction of order #{$order->id}","order", $deductable);
                            }else if($bonus->balance >= 0){
                                $bonusDeducted = $bonus->balance;
                                $order->bonus_paid = $bonusDeducted;
                                Bonus::deduct($request->user()->id, "Deduction of order #{$order->id}","order",$bonus->balance);
                            }
                        }
                    }
                    if($order->status == 'collected'){
                        $debit = $bonusDeducted;
                        //ServiceCharge::add($order->lifter_id,"Bonus addition of order #{$order->id}", "order", $debit);
                    }else{
                        $debit = ($order->service->s_charges * $order->qty) - $bonusDeducted;
                        ServiceCharge::deduct($order->lifter_id,"Service charges of order #{$order->id}", "order", $debit);
                    }
                    $order->payable_amount = (($order->qty * $order->price) + $order->charges ) - $bonusDeducted;
                }
                // Confirmed Order
                $order->status = 'confirmed';
                $order->payment_id = $request->paymentType;
                $order->confirmed_time = $dateTime;
            }
            $order->save();
            //
            DB::commit();
            $message = "Order for {$order->service->s_name} of {$order->qty} is {$status}.";
            event(new UpdateLifterEvent($order->lifter_id, $order));
            if($status == 'canceled'){
                $data = ['order_id' => $order->id, 'type' => 'order'];
                AndroidNotifications::toLifter("Order $status", $message, $order->lifter->pushToken, $data);
                return response()->json(['status'=>true, 'data' => [ "msg" => "Order $status", ]], 200);
            }else{
                $data = ['order_id' => $order->id, 'type' => 'confirmed_order', "amount" => $order->payable_amount];
                AndroidNotifications::toLifter("Order $status", $message, $order->lifter->pushToken, $data);
                return response()->json(['status'=>true, 'data' => [ "msg" => "Order $status", "amount" => $order->payable_amount ]], 200);
            }
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
