<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\User;
use App\Order;
use App\Http\Resources\Milk\Order as OrderResource;
use App\OrderDetail;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Helpers\AndroidNotifications;

class MilkOrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        try{
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
            $order->consumer_id = $request->consumer_id;
            $order->qty = $request->qty;
            $order->price = $request->price;
            $order->address = $request->address;
            $order->longitude = $request->longitude;
            $order->latitude = $request->latitude;
            $order->save();

            $orderDetail = new OrderDetail();
            $orderDetail->order_id = $order->id;
            $orderDetail->lifter_id = $request->lifter_id;
            $orderDetail->status = 'pending';
            $orderDetail->save();

            $message = "Place order of $order->qty liter of milk. Please deliver as earlist.";
            // Send Notification to Lifter
            $notification = AndroidNotifications::to($request->user()->name, $message, $lifter->pushToken,[]);
            // All Done respones back to consumer
            $success = ['msg' => 'Order Placed Successfully', 'notification' => $notification];
            return response()->json(['status'=>true, 'data' => $success], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    public function pendingMilkOrders(Request $request)
    {
        try{
            $orders = Order::where('consumer_id',$request->user()->id)->where('status','pending')->get();
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
            $order->status = 'accepted';
            $order->details()->insert([
                'lifter_id' => $request->user()->id,
                'status' => 'accepted',
            ]);
            $data = [ 'message' => "Order Accepted Sucessfully"];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }
}
