<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Helpers\AndroidNotifications;
use Carbon\Carbon;
use App\Http\Resources\Milk\Order as OrderResource;
use App\User;
use App\Order;
use Validator;
use DB;

class MilkOrderController extends Controller
{

    public function getNewOrderDetails($orderid)
    {
        try{
            $order = OpenOrder::with('consumer')->where('id',$orderid)->first();
            if($order == null){
                return response()->json(['status'=>false, 'data' => ['Order assigned to anotherone']], 200);
            }
            return response()->json(['status'=>true, 'data' => $order], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function orderAccept(Request $request)
    {
        try{
            DB::beginTransaction();
            $order = Order::find($request->orderid);
            if($order == null){
                DB::rollBack();
                return response()->json(['status'=>false, 'data' => false ], 200);
            }
            $order->lifter_id = $request->user()->id;
            $order->status = 'accepted';
            $order->accepted_time = Carbon::now()->toDateTimeString();
            $order->save();
            DB::commit();
            return response()->json(['status'=>true, 'data' => "Order Accepted"], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function inProcessOrders(Request $request)
    {
        try{
            $orders = Order::where('lifter_id',$request->user()->id)
            ->where('status','!=','delivered')->get();
            $orders =  OrderResource::collection($orders);
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