<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\AndroidNotifications;

use App\Bonus;
use App\Wallet;
use App\Subscription;
use DB;

class ScheduleOrderMorning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        date_default_timezone_set('Asia/Karachi');
        $date = \Carbon\Carbon::now();
        $nextHour = $date->hour + 2;
        $t1 = "$nextHour:00:00";
        $t2 = "$nextHour:59:00";

        $daily = \App\Subscription::where('status',1)->where('subscribe_type','daily')->whereBetween('delivery_time', [$t1, $t2])->get();
        foreach($daily as $sOrder){
            $this->createOrder($sOrder->order_id);
        }

        $weekdays = \App\Subscription::where('status',1)->where('subscribe_type','weekdays')->whereJsonContains('days',$date->dayOfWeek)->whereBetween('delivery_time', [$t1, $t2])->get();
        foreach($weekdays as $sOrder){
            $this->createOrder($sOrder->order_id);
        }

        $monthly = \App\Subscription::where('status',1)->where('subscribe_type','monthly')->whereJsonContains('days',$date->day)->whereBetween('delivery_time', [$t1, $t2])->get();
        foreach($monthly as $sOrder){
            $this->createOrder($sOrder->order_id);
        }

        $this->info('Orders Created');
    }

    public function createOrder($id){
        try {
            DB::beginTransaction();
            $settings = config('ohyes.consumer');
            $myOrder = \App\CartOrder::find($id);
            $order = new \App\CartOrder();
            $order->consumer_id = $myOrder->consumer_id;
            $order->payment_id = $myOrder->payment_id;
            $order->address_id = $myOrder->address_id;
            $order->charges = $myOrder->charges;
            $order->note = $myOrder->note;
            $order->type = 2;
            $order->consumer_bonus = 0;
            $order->store_amount = 0;
            $order->lifter_amount = 0;
            $order->payable_amount = 0;
            $order->status = 'created';
            $order->consumer_wallet = 0;
            $order->bullet_delivery = $myOrder->bullet_delivery;
            $order->delivery_time = $myOrder->delivery_time;
            $order->save();
            
            // Order Details Entry
            
            $productDetails = [];
            $payableAmount = 0;
            $bonusDeduction = 0;
            $smsmessage = "";
            foreach( $myOrder->details as $detail){
                $product = $produts->find($detail->product_id);
                $qty = $detail->qty;
                $productDetails[] = ['order_id' => $order->id, 'product_id' => $product->id, 'price' => $product->sale_price, 'qty' => $qty];
                $payableAmount += ($product->sale_price * $qty);
                $bonusDeduction +=  ( $product->bonus_deduction  * $qty);
                $smsmessage .= "{$product->name} x $qty\n";
            }
            \App\CartOrderDetail::insert($productDetails);
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
            if($bonusDeduction > 0){
                $bonus = Bonus::balance($order->consumer_id);
                if($bonus != null){
                    $bonusAmount = $bonus->balance >= $bonusDeduction ? $bonusDeduction :  $bonus->balance;
                    Bonus::deduct($order->consumer_id, "Deduction of order #{$order->id}","order",$bonusAmount);
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
            $msg = "Subscribe Order #{$order->id}\n {$order->consumer->name} {$order->consumer->mobile}\n";
            $msg .= $smsmessage;
            $msg .= "Time: {$order->delivery_time}";
            $msgresponse = \App\Helpers\SmsHelper::send("03088608825", $msg);
            $msgresponse = \App\Helpers\SmsHelper::send("03017374750", $msg);
            return true;
        } catch(Exception $e){
            DB::rollBack();
            return false;
        }
    }
}
