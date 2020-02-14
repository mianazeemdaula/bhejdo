<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\Auth;
use Validator;
use DB;

use App\User;
use App\Order;
use App\OpenOrder;
use App\Delivery;
use App\Helpers\AndroidNotifications;
use App\Helpers\OrderProcess;
use App\Http\Resources\Milk\Order as OrderResource;


class MilkOrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        try{
            DB::beginTransaction();
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
            
            $order = new OpenOrder();
            $order->consumer_id = $request->user()->id;
            $order->qty = $request->qty;
            $order->delivery_time = $request->delivery_time;
            $order->price = $request->price;
            $order->address = $request->address;
            $order->longitude = $request->longitude;
            $order->latitude = $request->latitude;
            $order->service_id = $request->service_id;
            $order->save();
            DB::commit();
            $response = OrderProcess::newOrder($order);
            $data = ['msg' => 'Order Placed Successfully', 'response' => $response];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function placeOrderOld(Request $request)
    {
        try{
            DB::beginTransaction();
            $validator = Validator::make( $request->all(), [
                'consumer_id' => 'required',
                'lifter_id' => 'required',
                'qty' => 'required',
                'price' => 'required',
                'address' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }

            $lifter = User::findOrFail($request->lifter_id);
            
            $order = new Order();
            $order->lifter_id = $request->lifter_id;
            $order->consumer_id = $request->user()->id;
            $order->qty = $request->qty;
            $order->delivery_date = $request->delivery_date;
            $order->delivery_time = $request->delivery_time;
            $order->price = $request->price;
            $order->address = $request->address;
            $order->longitude = $request->longitude;
            $order->latitude = $request->latitude;
            $order->save();

            $delivery = new Delivery();
            $delivery->order_id = $order->id;
            $delivery->lifter_id = $request->lifter_id;
            $delivery->status = 'pending';
            $delivery->save();

            DB::commit();

            $message = "Place order of $order->qty liter of milk. Please deliver as earlist.";
            // Send Notification to Lifter
            $args =  ["type" => 'order', 'order_id' => $order->id ];
            $notification = AndroidNotifications::toLifter($request->user()->name, $message, $lifter->pushToken,$args);
            // All Done respones back to consumer
            $success = ['msg' => 'Order Placed Successfully', 'notification' => $notification];
            return response()->json(['status'=>true, 'data' => $success], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function pendingMilkOrders(Request $request)
    {
        try{
            $orders = Order::where('consumer_id',$request->user()->id)
            ->where('status','!=','shipped')->where('status','!=','delivered')->get();
            $orders = OrderResource::collection($orders);
            $data = [ 'orders' => $orders];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function milkOrders(Request $request)
    {
        try{
            $orders = Order::where('consumer_id',$request->user()->id)->where('status',$request->type)->get();
            $orders = OrderResource::collection($orders);
            $data = [ 'orders' => $orders];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function confirmOrder(Request $request)
    {
        try{
            $order = Order::findOrFail($request->order_id);
            $order->status = 'received';
            $order->save();
            $order->details()->insert([
                'lifter_id' => $request->user()->id,
                'order_id' => $request->order_id,
                'status' => 'received',
            ]);
            $message = 'Order received by customer';
            $notification = AndroidNotifications::toConsumer($request->user()->name, $message, $order->consumer->pushToken,[]);
            $data = [ 'message' => "Order received by customer", 'notification' => $notification];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
