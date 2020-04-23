<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\CartOrder;

class CartOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
            // $validator = Validator::make( $request->all(), [
            //     'consumer_id' => 'required',
            //     'qty' => 'required',
            //     'price' => 'required',
            //     'address' => 'required',
            //     'latitude' => 'required',
            //     'longitude' => 'required',
            //     'service_id' => 'required'
            // ]);
    
            // if ($validator->fails()) {
            //     return response()->json(['error'=>$validator->errors()], 401);
            // }
            $cartItems =  json_decode($request->items);
            $productIds = [];
            foreach( $cartItems as $id => $qty){
                $productIds[] = $id;
            }
            $produts = \App\Product::whereIn('id',$productIds)->get();

            $productDetails = [];
            foreach( $produts as $product){
                $productDetails[] = ['order_id' => 2, 'product_id' => $id, 'price' => $product->sale_price, 'qty' => 1];
            }
            return response()->json(['status'=>true, 'data' => $productDetails ], 200);

            $order = new CartOrder();
            $order->consumer_id = $request->user()->id;
            $order->payment_id = $request->paymentType;
            $order->address_id = $request->address;
            $order->charges = $request->charges;
            $order->note = $request->note;
            $order->type = 1;
            $order->consumer_bonus = $request->consumer_bonus;
            $order->store_amount = 0;
            $order->lifter_amount = 0;
            $order->payable_amount = $request->payable;
            $order->status = 'created';
            $order->consumer_wallet = $request->address;
            $order->deliver_time = \App\Helpers\TimeHelper::parseTime($request->deliveryTime);
            $order->save();
            // $response = OrderProcess::orderAssign($order);
            DB::commit();
            // $data = ['msg' => 'Order has placed successfully', 'response' => $response];
            return response()->json(['status'=>true, 'data' => $data], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
