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
}
