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

use App\Http\Resources\Milk\Order as OrderResource;
use App\Helpers\AndroidNotifications;
use App\Helpers\OrderProcess;

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
                'delivery_time' => 'required',
                'service_id' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }

            $service = Service::findOrFail($request->service_id);
            
            $order = new Order();
            $order->consumer_id = $request->user()->id;
            $order->lifter_id = 2;
            $order->service_id = $request->service_id;
            $order->qty = $request->qty;
            $order->price = $service->s_price;
            $order->note = $service->note;
            $order->note = $service->note;
            $order->delivery_time = $request->delivery_time;
            $order->address = $request->address;
            $order->longitude = $request->longitude;
            $order->latitude = $request->latitude;
            if($request->has('sample')){
                $order->type = 3;
            }
            if($request->qty < $service->min_qty){
                $order->charges = $service->min_qty_charges;
            }else{
                $order->charges = 0;
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
