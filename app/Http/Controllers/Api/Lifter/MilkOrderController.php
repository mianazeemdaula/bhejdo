<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Order;
use App\Http\Resources\Milk\Order as OrderResource;
use App\OrderDetail;
use App\Delivery;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Helpers\AndroidNotifications;
use DB;

class MilkOrderController extends Controller
{
    public function pendingOrders(Request $request)
    {
        try{
            $orders = Order::where('lifter_id',$request->user()->id)->where('status','pending')->get();
            $orders = OrderResource::collection($orders);
            $data = [ 'orders' => $orders];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function updateOrder(Request $request)
    {
        try{
            $data = [];
            DB::beginTransaction();
            $order = Order::findOrFail($request->order_id);
            $order->status = $request->status;
            $order->save();
            $delivery = new Delivery();
            $delivery->lifter_id = $request->user()->id;
            $delivery->order_id =  $request->order_id;
            $delivery->status = $request->status;
            $delivery->save();
            $message = "Your Order #  $order->id of $order->qty ltr on dated $order->created_at is $request->status sucessfully";
            $notification = AndroidNotifications::toConsumer($request->user()->name, $message, $order->consumer->pushToken,[]);
            $data = [ 'message' => "Your Order #  $order->id is $request->status sucessfully", 'notification' => $notification];
            DB::commit();
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function getOrder($orderId)
    {
        try{
            $order = Order::findOrFail($orderId);
            $order = new OrderResource($order);
            $data = [ 'order' => $order];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}