<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Address;
use App\User;
use App\CartOrder;
use App\CartOrderDetail;
use App\Bonus;
use App\Wallet;

use DB;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\Admin\Cart\CreateOrderForm;

class CartController extends Controller
{
    use FormBuilderTrait;

    public function index($user, $address)
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($user, $address)
    {
        $user = User::find($user);
        $address = Address::find($address);
        $form = $this->form(CreateOrderForm::class, [
            'method' => 'POST',
            'class' => 'form-horizontal',
            'url' => route('user.address.cart.store',[$user->id, $address->id])
        ]);
        $products = \App\Product::where('city_id',$user->city_id)->get();
        return view('pages.admin.user.address.cart.create', compact('form','user','address','products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $user, $address)
    {
        DB::beginTransaction();
        try{
            
            $settings = config('ohyes.consumer');
            $order = new CartOrder();
            $order->consumer_id = $user;
            $order->payment_id = 1;
            $order->address_id = $address;
            $order->charges = 0;
            $order->note = "";
            $order->type = 1;
            $order->consumer_bonus = 0;
            $order->store_amount = 0;
            $order->lifter_amount = 0;
            $order->payable_amount = 0;
            $order->status = 'created';
            $order->consumer_wallet = 0;
            $order->bullet_delivery = 0;
            $order->delivery_time = $request->delivery_time;
            $order->save();
            
            // Order Details Entry
            $cartItems = $request->products;
            $produts = \App\Product::whereIn('id',$cartItems)->get();

            $productDetails = [];
            $payableAmount = 0;
            $bonusDeduction = 0;
            $smsmessage = "";
            $quantities = $request->qty;
            for($i = 0; $i < count($cartItems) ; $i++ ){
                $product = $produts->find($cartItems[$i]);
                $qty = $quantities[$i];
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
                $promoCode = strtoupper(trim($request->coupon));
                $response = \App\Helpers\OfferProcess::processOffer($request->user()->id,$promoCode, $payableAmount);
                if($response['status'] == true){
                    if($response['data']['credit'] == 0){
                        $bonusAmount = $response['data']['amount'];
                    }
                    $order->coupon = $promoCode;
                    $order->consumer_bonus = $bonusAmount;
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
            DB::commit();
            $msg = "New Order\n #{$order->id} - {$order->created_at}\n {$order->consumer->name} {$order->consumer->mobile}\n";
            $msg .= $smsmessage;
            // $msgresponse = \App\Helpers\SmsHelper::send("03088608825", $msg);
            // $msgresponse = \App\Helpers\SmsHelper::send("03017374750", $msg);
            \App\Jobs\SendSmsJob::dispatch("03017374750", $msg, 'smsapi')->delay(now()->addSeconds(2));
            \App\Jobs\SendSmsJob::dispatch("03088608825", $msg, 'smsapi')->delay(now()->addSeconds(10));
            // $data = ['msg' => 'Order has placed successfully', 'response' => $response];
            return redirect()->route('admin.order.index')->with('status', 'Order created Successfully');
        }catch(Expection $ex){
            DB::rollBack();
            return redirect()->back()->with('status', 'Something went wrong!');
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
