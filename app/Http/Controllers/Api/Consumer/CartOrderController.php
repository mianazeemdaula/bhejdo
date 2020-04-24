<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\CartOrder;
use App\CartOrderDetail;
use App\Wallet;
use App\Bonus;

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
            // $cartItems =  json_decode($request->items);
            // $productIds = [];
            // foreach( $cartItems as $id => $qty){
            //     $productIds[] = $id;
            // }
            // $produts = \App\Product::whereIn('id',$productIds)->get();

            // $productDetails = [];
            // $lifterAmount = 0;
            // $storeAmount = 0;
            // foreach( $cartItems as $id => $qty){
            //     $product = $produts->find($id);
            //     $productDetails[] = ['order_id' => 2, 'product_id' => $product->id, 'price' => $product->sale_price, 'qty' => $qty];
            //     $storeAmount += ($product->sale_price * $qty);
            // }
            //return response()->json(['status'=>true, 'data' => $productDetails ], 200);

            $order = new CartOrder();
            $order->consumer_id = $request->user()->id;
            $order->payment_id = $request->paymentType;
            $order->address_id = $request->address;
            $order->charges = $request->charges;
            $order->note = $request->note;
            $order->type = 1;
            $order->consumer_bonus = $request->consumerBonus;
            $order->store_amount = 0;
            $order->lifter_amount = 0;
            $order->payable_amount = $request->payable;
            $order->status = 'created';
            $order->consumer_wallet = 0;
            $order->bullet_delivery = $request->bulletDelivery;
            $order->delivery_time = \App\Helpers\TimeHelper::parseTime($request->deliveryTime);
            $order->save();
            
            // Order Details Entry
            $cartItems =  json_decode($request->items);
            $productIds = [];
            foreach( $cartItems as $id => $qty){
                $productIds[] = $id;
            }
            $produts = \App\Product::whereIn('id',$productIds)->get();

            $productDetails = [];
            $payableAmount = 0;
            $bonusDeduction = 0;
            foreach( $cartItems as $id => $qty){
                $product = $produts->find($id);
                $productDetails[] = ['order_id' => $order->id, 'product_id' => $product->id, 'price' => $product->sale_price, 'qty' => $qty];
                $payableAmount += ($product->sale_price * $qty);
                $bonusDeduction +=  ( $product->bonus_deduction  * $qty);
            }
            CartOrderDetail::insert($productDetails);
            // Payable ammount Sale Price + Charges - Bonus
            $charges = 0;
            if($order->bullet_delivery == 1){
                $charges = 50;
            }else if($payableAmount < 1000){
                $charges = 30;
            }else{
                $charges = 0;
            }


            // Caculate wallet amount on wallet payment
            $walletAmount = 0;
            if($order->payment_id == 2){
                $wallet = Wallet::balance($order->consumer_id);
                if($wallet != null){
                    if($wallet->balance >= $payableAmount){
                        $walletAmount = $payableAmount;
                        Wallet::deduct($order->consumer_id, "Deduction of order #{$order->id}","order",$payableAmount);
                    }else{
                        $walletAmount = $payableAmount - $wallet->balance;
                        Wallet::deduct($order->consumer_id, "Deduction of order #{$order->id}","order",$walletAmount);
                    }
                }
            }

            // Caculate bonus amount on bonus deduction
            $bonusAmount = 0;
            if($bonusDeduction > 0){
                $bonus = Bonus::balance($order->consumer_id);
                if($bonus != null){
                    if($bonus->balance >= $bonusDeduction){
                        $bonusAmount = $bonusDeduction;
                        Bonus::deduct($order->consumer_id, "Deduction of order #{$order->id}","order",$bonusAmount);
                    }else{
                        $bonusAmount = $bonusDeduction - $bonus->balance;
                        Bonus::deduct($order->consumer_id, "Deduction of order #{$order->id}","order",$bonusAmount);
                    }
                }
            }
            
            $payableAmount = ($payableAmount - $bonusAmount) + $charges;
            $order->store_amount = ($payableAmount - $bonusAmount) - $charges;
            $order->charges = $charges;
            $order->lifter_amount = $payableAmount;
            $order->payable_amount = $payableAmount;
            $order->consumer_bonus = $bonusAmount;
            $order->consumer_wallet = $order->payment_id == 2 ? $walletAmount : 0;
            $order->save();
            DB::commit();
            // $data = ['msg' => 'Order has placed successfully', 'response' => $response];
            return response()->json(['status'=>true, 'data' => "profile"], 200);
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
