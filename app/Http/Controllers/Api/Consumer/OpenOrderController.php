<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\OpenOrder;
use App\Helpers\OrderProcess;

class OpenOrderController extends Controller
{
    public function create(Request $request)
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

            $service = Service::findOrFail($request->service_id);
            
            $order = new OpenOrder();
            $order->consumer_id = $request->user()->id;
            $order->service_id = $request->service_id;
            $order->qty = $request->qty;
            $order->price = $service->s_price;
            $order->delivery_time = $request->delivery_time;
            $order->address = $request->address;
            $order->longitude = $request->longitude;
            $order->latitude = $request->latitude;
            if($order->exists('sample')){
                $order->type = 3;
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
}
