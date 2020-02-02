<?php

namespace App\Http\Controllers\Api\Lifter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Order;
use App\Http\Resources\Milk\Order as OrderResource;
use App\OrderDetail;
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
            DB::transaction(function () use($request) {
                $order = Order::findOrFail($request->order_id);
                $order->status = $request->status;
                $order->save();
                $order->delivery()->insert([
                    'lifter_id' => $request->user()->id,
                    'order_id' => $request->order_id,
                    'status' => $request->status,
                ]);
                $message = 'You order is '.$request->status;
                $notification = AndroidNotifications::toConsumer($request->user()->name, $message, $order->consumer->pushToken,[]);
                $data = [ 'message' => "Order $request->status sucessfully", 'notification' => $notification];
            }, 2);
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}