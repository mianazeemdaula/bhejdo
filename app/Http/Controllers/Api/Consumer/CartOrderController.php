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
    public function index(Request $request)
    {
        DB::beginTransaction();
        try{
            $orders = CartOrder::where('consumer_id',$request->user()->id)->orderBy('id','desc')->get();
            $orders = \App\Http\Resources\V2\Consumer\OrderResource::collection($orders);
            return response()->json(['status'=>true, 'data' => ['orders' => $orders]], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
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
            
            $settings = config('ohyes.consumer');

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
            $smsmessage = "";
            foreach( $cartItems as $id => $qty){
                $product = $produts->find($id);
                $productDetails[] = ['order_id' => $order->id, 'product_id' => $product->id, 'price' => $product->sale_price, 'qty' => $qty];
                $payableAmount += ($product->sale_price * $qty);
                $bonusDeduction +=  ( $product->bonus_deduction  * $qty);
                $smsmessage .= "{$product->name} x $qty\n";
            }
            CartOrderDetail::insert($productDetails);
            // Payable ammount Sale Price + Charges - Bonus
            $charges = 0;
            if($order->bullet_delivery == 1){
                $charges = $settings['bullet_service_charges'];
            }else if($payableAmount < $settings['delivery_free_shopping']){
                $charges = $settings['normal_delivery_fee'];;
            }else{
                $charges = 0;
            }

            // Caculate bonus amount on bonus deduction
            $bonusAmount = 0;

            // Check if user aplied coupon elase deduct bonus if have
            if($request->has('coupon')){
                $promoCode = trim($request->coupon);
                $response = \App\Helpers\OfferProcess::processOffer($request->user()->id,$promoCode, $payableAmount);
                if($response['status'] == true){
                    if(in_array('amount', $response['data'])){
                        $bonusAmount = $response['data']['amount'];
                    }
                    $order->coupon = $promoCode;
                }
            }else{
                if($bonusDeduction > 0){
                    $bonus = Bonus::balance($order->consumer_id);
                    if($bonus != null){
                        $bonusAmount = $bonus->balance >= $bonusDeduction ? $bonusDeduction :  $bonus->balance;
                        Bonus::deduct($order->consumer_id, "Deduction of order #{$order->id}","order",$bonusAmount);
                    }
                }
            }
            
            $payableAmount = ($payableAmount - $bonusAmount) + $charges;

            // Caculate wallet amount on wallet payment
            $walletAmount = 0;
            if($order->payment_id == 2){
                $wallet = Wallet::balance($order->consumer_id);
                if($wallet != null){
                    $walletAmount = $wallet->balance >= $payableAmount ? $payableAmount :  $wallet->balance;
                    Wallet::deduct($order->consumer_id, "Deduction of order #{$order->id}","order",$walletAmount);
                }
            }

            $order->store_amount = $payableAmount - $charges;
            $order->charges = $charges;
            $order->lifter_amount = $payableAmount;
            $order->payable_amount = $payableAmount;
            $order->consumer_bonus = $bonusAmount;
            $order->consumer_wallet = $order->payment_id == 2 ? $walletAmount : 0;
            $order->save();
            $profile = new \App\Http\Resources\Profile\ConsumerProfile($request->user());
            DB::commit();

            $msg = "New Order\n #{$order->id} - {$order->created_at}\n {$order->consumer->name} {$order->consumer->mobile}\n";
            $msg .= $smsmessage;
            $msgresponse = \App\Helpers\SmsHelper::send("03088608825", $msg);
            $msgresponse = \App\Helpers\SmsHelper::send("03017374750", $msg);
            // $data = ['msg' => 'Order has placed successfully', 'response' => $response];
            return response()->json(['status'=>true, 'data' => $order, 'profile' => $profile], 200);
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
        DB::beginTransaction();
        try{
            DB::commit();
            $status = strtolower($request->status);
            $user = null;
            if($status == 'canceled'){
                $user = $request->user()->id;
            }
            $response = \App\Helpers\OrderProcess::updateCartOrder($id, $status, $user);
            return response()->json(['status'=>true, 'data' => $response], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json(['status'=>false, 'data'=>"$ex"], 401);
        }
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
