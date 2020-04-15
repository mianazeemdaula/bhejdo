<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\AndroidNotifications;

use App\Bonus;
use App\ScheduleOrder;
use DB;
class ScheduleOrderMorning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduleorder:morning';

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
        $nextHour = $date->hour + 1;
        $t1 = "$nextHour:00:00";
        $t2 = "$nextHour:59:00";

        $daily = \App\ScheduleOrder::where('status',1)->where('subscribe_type','daily')->whereBetween('delivery_time', [$t1, $t2])->get();
        foreach($daily as $sOrder){
            $this->createOrder($sOrder);
        }

        $weekdays = \App\ScheduleOrder::where('status',1)->where('subscribe_type','weekdays')->whereJsonContains('days',$date->dayOfWeek)->whereBetween('delivery_time', [$t1, $t2])->get();
        foreach($weekdays as $sOrder){
            $this->createOrder($sOrder);
        }

        $monthly = \App\ScheduleOrder::where('status',1)->where('subscribe_type','monthly')->whereJsonContains('days',$date->day)->whereBetween('delivery_time', [$t1, $t2])->get();
        foreach($monthly as $sOrder){
            $this->createOrder($sOrder);
        }

        $this->info('Orders Created');
    }

    public function createOrder(ScheduleOrder $sOrder){
        try {
            DB::beginTransaction();
            $order = new \App\Order();
            $order->consumer_id = $sOrder->consumer_id;
            $order->lifter_id = $sOrder->lifter_id;
            $order->service_id = $sOrder->service_id;
            $order->qty = $sOrder->qty;
            $order->price = $sOrder->service->s_price;
            $order->note = "";
            $order->address = $sOrder->address;
            $order->longitude = $sOrder->longitude;
            $order->latitude = $sOrder->latitude;
            $charges = $sOrder->qty <= $sOrder->service->min_qty ? $sOrder->service->min_qty_charges : 0;
            $order->charges = $charges;
            $order->deliver_time = $sOrder->delivery_time;
            $order->delivery_time = \Carbon\Carbon::now();
            $order->type = 2;
            // $bonus = Bonus::balance($sOrder->consumer_id);
            $bonusDeducted = 0;
            // if($bonus != null){
            //     $deductable = $sOrder->qty * 10;  
            //     if($bonus->balance >= $deductable){
            //         $bonusDeducted = $deductable;
            //         $order->bonus = $bonusDeducted;
            //         Bonus::deduct($order->consumer_id, "Deduction of subsribed order #{$sOrder->id}","order", $bonusDeducted);
            //     }else if($bonus->balance >= 0){
            //         $bonusDeducted = $bonus->balance;
            //         $order->bonus = $bonusDeducted;
            //         Bonus::deduct($order->consumer_id, "Deduction of subsribed order #{$sOrder->id}","order", $bonusDeducted);
            //     }
            // }
            $order->status = 'assigned';
            $order->payable_amount = (($sOrder->qty * $sOrder->service->s_price) + $charges ) - $bonusDeducted;
            $order->save();
            DB::commit();
            $data = ['order_id' => $order->id, 'type' => 'order'];
            AndroidNotifications::toLifter("Schedule Order", $message, $order->lifter->pushToken, $data);
        } catch(Exception $e){
            DB::rollBack();
            return response()->json(['status'=>false, 'error' => "Internal Server Error" ], 405);
        }
    }
}
